<?php

namespace App\Http\Controllers;
use App\Models\Rotation;
use App\Models\TrainingUnit;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MedicalReport;
use Barryvdh\DomPDF\Facade\Pdf;

class HospitalAppointmentController extends Controller
{
    // แสดงรายการที่มีสถานะ 'sent'
    public function sentAppointments(Request $request)
    {
        $status = $request->input('status', 'sent');
        $date = $request->input('date');
        $caseType = $request->input('case_type');
        $rotationId = $request->input('rotation_id');
        $trainingUnitId = $request->input('training_unit_id');
        $todayStatus = $request->input('today_status');

        // ✅ ถ้าเป็นกรณี today-status ให้แยกโครงสร้างต่างหาก
        if ($status === 'today-status') {
            $targetDate = $date ?: now()->toDateString();

            $query = MedicalReport::query();

            // ✅ ปรับเงื่อนไข: ค้นหาทั้งวันที่นัดและวันที่ขาดนัด
            $query->whereHas('appointment', function ($q) use ($targetDate, $caseType) {
                $q->where(function ($subQuery) use ($targetDate) {
                    // ค้นหาการนัดหมายที่มีวันที่ตรงกับ targetDate
                    $subQuery->whereDate('appointment_date', $targetDate)
                        // หรือมีการขาดนัดในวันที่ targetDate
                        ->orWhere(function ($missedQuery) use ($targetDate) {
                            $missedQuery->where('was_missed', 1)
                                ->whereDate('missed_appointment_date', $targetDate);
                        });
                });

                if ($caseType && $caseType !== 'all') {
                    $q->where('case_type', $caseType);
                }
            });

            if ($rotationId) {
                $query->whereHas('soldier.rotation', function ($q) use ($rotationId) {
                    $q->where('id', $rotationId);
                });
            }

            if ($trainingUnitId) {
                $query->whereHas('soldier', function ($q) use ($trainingUnitId) {
                    $q->where('training_unit_id', $trainingUnitId);
                });
            }

            $reports = $query->with(['appointment.checkin.treatment', 'soldier.trainingUnit', 'soldier.rotation'])->get();

            // ✅ อัปเดตการกรองสถานะ
            if ($todayStatus && $todayStatus !== 'all') {
                $reports = $reports->filter(function ($report) use ($todayStatus, $targetDate) {
                    $a = $report->appointment;
                    $c = $a->checkin ?? null;
                    $t = $c->treatment ?? null;

                    if (!$a)
                        return false;

                    // ✅ ตรวจสอบว่าวันที่กำลังดูเป็นวันไหน
                    $viewingDate = $targetDate;
                    $appointmentDate = \Carbon\Carbon::parse($a->appointment_date)->format('Y-m-d');
                    $hasMissed = $a->was_missed && $a->missed_appointment_date;
                    $missedDate = $hasMissed ? \Carbon\Carbon::parse($a->missed_appointment_date)->format('Y-m-d') : null;

                    $isViewingMissedDate = $hasMissed && ($viewingDate === $missedDate);
                    $isViewingAppointmentDate = ($viewingDate === $appointmentDate);

                    // ✅ กำหนดสถานะตามวันที่ที่กำลังดู
                    if ($isViewingMissedDate) {
                        $status = 'ไม่มาตามนัด (นัดหมายใหม่แล้ว)';
                    } elseif ($isViewingAppointmentDate) {
                        // สถานะตามการรักษาจริง
                        if ($a->status === 'scheduled' && optional($c)->checkin_status === 'not-checked-in') {
                            $status = 'ยังไม่ได้ทำการรักษา';
                        } elseif ($a->status === 'scheduled' && optional($c)->checkin_status === 'checked-in' && optional($t)->treatment_status === 'not-treated') {
                            $status = 'อยู่ระหว่างการรักษา';
                        } elseif ($a->status === 'completed' && optional($c)->checkin_status === 'checked-in' && optional($t)->treatment_status === 'treated') {
                            $status = 'รักษาสำเร็จ';
                        } elseif ($a->status === 'missed') {
                            $status = 'ไม่มาตามนัด';
                        } else {
                            $status = 'ยังไม่ได้ทำการรักษา'; // ค่าเริ่มต้นสำหรับวันนัดใหม่
                        }
                    } else {
                        // วันอื่นๆ - สถานะปกติ
                        if ($a->status === 'scheduled' && optional($c)->checkin_status === 'not-checked-in') {
                            $status = 'ยังไม่ได้ทำการรักษา';
                        } elseif ($a->status === 'scheduled' && optional($c)->checkin_status === 'checked-in' && optional($t)->treatment_status === 'not-treated') {
                            $status = 'อยู่ระหว่างการรักษา';
                        } elseif ($a->status === 'completed' && optional($c)->checkin_status === 'checked-in' && optional($t)->treatment_status === 'treated') {
                            $status = 'รักษาสำเร็จ';
                        } elseif ($a->status === 'missed') {
                            $status = 'ไม่มาตามนัด';
                        } else {
                            $status = 'ไม่ทราบสถานะ';
                        }
                    }

                    return $status === $todayStatus;
                });
            }

            $medicalReports = $reports;
            $selectedDate = $targetDate;

        } else {
            // 🔎 สำหรับ status อื่น (sent, scheduled, missed, scheduledComplete, todaymakeappointmenttoday)
            $query = MedicalReport::query();

            if ($status === 'sent') {
                $query->where('status', 'sent');
            }

            // ✅ รองรับ scheduled - แสดงทุกวันหรือกรองตามวันที่
            if ($status === 'scheduled') {
                $query->whereHas('appointment', function ($q) use ($date) {
                    $q->where('status', 'scheduled');

                    if ($date) {
                        $q->whereDate('appointment_date', $date);
                    }
                    // ลบเงื่อนไข whereDate แบบบังคับ เพื่อให้แสดงทุกวัน
                });

                if ($caseType && $caseType !== 'all') {
                    $query->whereHas('appointment', function ($q) use ($caseType) {
                        $q->where('case_type', $caseType);
                    });
                }
            }

            // ✅ แก้ไข scheduledComplete แบบ Debug
            if ($status === 'scheduledComplete') {
                // ✅ ขั้นตอนที่ 1: ดึงข้อมูลทั้งหมดก่อน (ไม่กรองวันที่)
                $query->whereHas('appointment', function ($q) use ($caseType) {
                    $q->whereIn('status', ['scheduled', 'completed', 'missed']);

                    if ($caseType && $caseType !== 'all') {
                        $q->where('case_type', $caseType);
                    }
                });

                // ✅ เพิ่มเงื่อนไข: ไม่รวม status 'in ER' ใน medical_report table
                $query->whereNotIn('status', ['in ER']);

                if ($rotationId) {
                    $query->whereHas('soldier.rotation', function ($q) use ($rotationId) {
                        $q->where('id', $rotationId);
                    });
                }

                if ($trainingUnitId) {
                    $query->whereHas('soldier', function ($q) use ($trainingUnitId) {
                        $q->where('training_unit_id', $trainingUnitId);
                    });
                }

                // ✅ ขั้นตอนที่ 2: ดึงข้อมูลทั้งหมด
                $allReports = $query->with([
                    'soldier',
                    'soldier.trainingUnit',
                    'soldier.rotation',
                    'appointment',
                    'vitalSign'
                ])->get();

                // ✅ ขั้นตอนที่ 3: กรองวันที่ใน PHP
                $targetDate = $date ?: now()->toDateString();

                $filteredReports = $allReports->filter(function ($report) use ($targetDate) {
                    if (!$report->appointment)
                        return false;

                    $appointmentDate = \Carbon\Carbon::parse($report->appointment->appointment_date)->format('Y-m-d');
                    $hasMissed = $report->appointment->was_missed && $report->appointment->missed_appointment_date;
                    $missedDate = $hasMissed ? \Carbon\Carbon::parse($report->appointment->missed_appointment_date)->format('Y-m-d') : null;

                    // ✅ แสดงถ้าตรงกับวันนัดหมายหรือวันขาดนัด
                    return ($appointmentDate === $targetDate) || ($hasMissed && $missedDate === $targetDate);
                });

                $medicalReports = $filteredReports;

                // ✅ Debug Log
                \Log::info('ScheduledComplete Debug', [
                    'total_reports' => $allReports->count(),
                    'filtered_reports' => $filteredReports->count(),
                    'target_date' => $targetDate,
                    'date_param' => $date,
                    'sample_appointment_dates' => $allReports->take(5)->map(function ($r) {
                        return [
                            'appointment_date' => $r->appointment ? $r->appointment->appointment_date : null,
                            'missed_date' => $r->appointment && $r->appointment->missed_appointment_date ? $r->appointment->missed_appointment_date : null,
                            'was_missed' => $r->appointment ? $r->appointment->was_missed : null
                        ];
                    })
                ]);
            }
            // ✅ รองรับ missed
            if ($status === 'missed') {
                $query->whereHas('appointment', function ($q) use ($status, $date) {
                    $q->where('status', $status);

                    if ($date) {
                        $q->whereDate('appointment_date', $date);
                    }
                });

                if ($caseType && $caseType !== 'all') {
                    $query->whereHas('appointment', function ($q) use ($caseType) {
                        $q->where('case_type', $caseType);
                    });
                }
            }

            // ✅ รองรับ todaymakeappointmenttoday
            if ($status === 'todaymakeappointmenttoday') {
                $query->whereHas('appointment', function ($q) use ($date) {
                    if ($date) {
                        $q->whereDate('created_at', $date);
                    } else {
                        $today = now()->toDateString();
                        $q->whereDate('created_at', $today);
                    }
                });

                if ($caseType && $caseType !== 'all') {
                    $query->whereHas('appointment', function ($q) use ($caseType) {
                        $q->where('case_type', $caseType);
                    });
                }
            }

            // กรองตาม rotation
            if ($rotationId) {
                $query->whereHas('soldier.rotation', function ($q) use ($rotationId) {
                    $q->where('id', $rotationId);
                });
            }

            // กรองตาม training unit
            if ($trainingUnitId) {
                $query->whereHas('soldier', function ($q) use ($trainingUnitId) {
                    $q->where('training_unit_id', $trainingUnitId);
                });
            }

            $medicalReports = $query->with([
                'soldier',
                'soldier.trainingUnit',
                'soldier.rotation',
                'appointment',
                'vitalSign'
            ])->get();
        }

        $rotations = Rotation::all();
        $trainingUnits = TrainingUnit::all();

        // ✅ Debug Log
        \Log::info('Appointment Filter Debug', [
            'status' => $status,
            'date' => $date,
            'case_type' => $caseType,
            'rotation_id' => $rotationId,
            'training_unit_id' => $trainingUnitId,
            'today_status' => $todayStatus,
            'results_count' => $medicalReports->count(),
            'target_date' => $targetDate ?? 'N/A'
        ]);

        return view('admin-hospital.approved_appointment', compact(
            'medicalReports',
            'rotations',
            'trainingUnits'
        ));
    }


    // อัปเดตสถานะเป็น 'approved' และสร้างนัดหมาย
    public function approveAppointment(Request $request)
    {
        // ตรวจสอบค่าที่ส่งมา
        $request->validate([
            'ids' => 'required|array',
            'appointment_date' => 'required|date',
            'appointment_location' => 'required|string',
            'case_type' => 'required|in:normal,critical',
        ]);

        foreach ($request->ids as $id) {
            // อัปเดตสถานะเป็น 'approved'
            MedicalReport::where('id', $id)->update(['status' => 'approved']);

            // บันทึกข้อมูลการนัดหมาย
            Appointment::create([
                'medical_report_id' => $id,
                'appointment_date' => $request->appointment_date,
                'appointment_location' => $request->appointment_location,
                'case_type' => $request->case_type,
                'status' => 'approved', // หลังจากนัดหมายเสร็จ สถานะเป็น 'scheduled'
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'นัดหมายสำเร็จ']);
    }


    public function scheduleAppointments(Request $request)
    {
        // ✅ Debug เช็คค่าที่ถูกส่งจาก Frontend
        dd($request->all());

        $request->validate([
            'medical_report_ids' => 'required|array',
            'medical_report_ids.*' => 'exists:medical_reports,id',
            'appointment_date' => 'required|date',
            'appointment_location' => 'required|string',
            'case_type' => 'required|in:normal,critical'
        ]);

        foreach ($request->medical_report_ids as $id) {
            Appointment::create([
                'medical_report_id' => $id,
                'appointment_date' => $request->appointment_date,
                'appointment_location' => $request->appointment_location,
                'case_type' => $request->case_type,
                'status' => 'scheduled'
            ]);

            // อัปเดตสถานะของ medical report เป็น 'approved'
            MedicalReport::where('id', $id)->update(['status' => 'approved']);
        }

        return response()->json(['status' => 'success', 'message' => 'นัดหมายสำเร็จ']);
    }









    public function download($id)
    {
        dd(config('dompdf.fonts'));

        $appointment = Appointment::with(['medicalReport.soldier', 'medicalReport.soldier.rotation', 'medicalReport.soldier.trainingUnit'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pdf.appointment', compact('appointment'));

        // ตั้งค่าฟอนต์เพิ่มเติม
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('defaultFont', 'Sarabun');
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->download("appointment-{$id}.pdf");
    }

    public function downloadAll(Request $request)
    {


        $query = Appointment::with(['medicalReport.soldier', 'medicalReport.soldier.rotation', 'medicalReport.soldier.trainingUnit']);

        if ($request->status === 'scheduled') {
            $query->where('status', 'scheduled');
        }

        if ($request->filled('case_type') && $request->case_type !== 'all') {
            $query->where('case_type', $request->case_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->get();

        $pdf = Pdf::loadView('pdf.all_appointments', compact('appointments'));

        // ตั้งค่าฟอนต์เพิ่มเติม
        $pdf->setPaper('a4', 'portrait');
        $pdf = Pdf::loadView('pdf.all_appointments', compact('appointments'))
            ->setOption('defaultFont', 'Sarabun');

        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->download("appointments-report-" . now()->format('YmdHis') . ".pdf");
    }

}
















