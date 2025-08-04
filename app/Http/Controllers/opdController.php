<?php

namespace App\Http\Controllers;
use App\Models\Rotation;
use App\Models\TrainingUnit;
use Illuminate\Http\Request;
use App\Models\MedicalReport;

use App\Models\Appointment;
use App\Models\Checkin;
use Carbon\Carbon;
use App\Models\MedicalDiagnosis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpdController extends Controller
{

    public function OpdCountdashboard()
    {
        $today = Carbon::today();

        // 1. จำนวนผู้ป่วยที่มีนัดหมายวันนี้
        $totalAppointmentsToday = Appointment::whereDate('appointment_date', $today)
            ->whereHas('medicalReport', function ($query) {
                $query->where('status', 'approved');
            })
            ->count();

        // Log ข้อมูลจำนวนการนัดหมาย
        Log::info("Total appointments today: {$totalAppointmentsToday}");

        // 2. จำนวนผู้ป่วยที่ยังไม่เช็กอิน
        $patientsNotCheckedInToday = Checkin::whereHas('appointment', function ($query) use ($today) {
            $query->whereDate('appointment_date', $today)
                ->whereHas('medicalReport', function ($query) {
                    $query->where('status', 'approved');
                });
        })->where('checkin_status', 'not-checked-in')->count();

        // Log ข้อมูลจำนวนผู้ป่วยที่ยังไม่เช็กอิน
        Log::info("Patients not checked-in today: {$patientsNotCheckedInToday}");

        // 3. จำนวนผู้ป่วยที่เช็กอินแล้ววันนี้
        $patientsCheckedInToday = Checkin::whereHas('appointment', function ($query) use ($today) {
            $query->whereDate('appointment_date', $today)
                ->whereHas('medicalReport', function ($query) {
                    $query->where('status', 'approved');
                });
        })
            ->where('checkin_status', 'checked-in')
            ->whereDoesntHave('treatment', function ($query) {
                $query->where('treatment_status', 'treated');
            })
            ->count();

        // Log ข้อมูลจำนวนผู้ป่วยที่เช็กอินแล้ว
        Log::info("Patients checked-in today: {$patientsCheckedInToday}");

        // 4. จำนวนผู้ป่วยที่รักษาเสร็จสิ้นในวันนี้
        $patientsTreatmentCompletedToday = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'completed')
            ->whereHas('medicalReport', function ($query) {
                $query->where('status', 'approved');
            })
            ->count();

        // Log ข้อมูลจำนวนผู้ป่วยที่รักษาเสร็จสิ้น
        Log::info("Patients treatment completed today: {$patientsTreatmentCompletedToday}");

        // 5. ดึงนัดหมายปกติที่นัดหมายวันนี้
        $normalAppointmentsToday = Appointment::with('medicalReport.soldier')
            ->whereDate('appointment_date', $today)
            ->where('case_type', 'normal')
            ->whereIn('status', values: ['scheduled', 'completed', 'missed'])  // รวมสถานะ scheduled และ completed
            ->whereHas('medicalReport', function ($query) {
                $query->where('status', 'approved');
            })
            ->get();

        // Log ข้อมูลจำนวนการนัดหมายปกติ
        Log::info("Normal appointments today: " . $normalAppointmentsToday->count());

        // 6. นับจำนวนผู้ป่วยที่นัดหมายแบบปกติวันนี้
        $normalAppointmentCount = $normalAppointmentsToday->count();

        // 7. ดึงนัดหมายวิกฤติวันนี้
        $criticalAppointments = Appointment::with('medicalReport.soldier')
            ->whereDate('appointment_date', $today)
            ->where('case_type', 'critical')
            ->whereIn('status', values: ['scheduled', 'completed', 'missed'])  // รวมสถานะ scheduled และ completed
            ->whereHas('medicalReport', function ($query) {
                $query->where('status', 'approved');
            })
            ->get();

        // Log ข้อมูลจำนวนการนัดหมายวิกฤติ
        Log::info("Critical appointments today: " . $criticalAppointments->count());

        // 8. นับจำนวนการนัดหมายวิกฤติวันนี้
        $criticalCount = $criticalAppointments->count();

        // ส่งข้อมูลไปยัง View
        return view('opd.dashboard_opd', compact(
            'totalAppointmentsToday',
            'patientsNotCheckedInToday',
            'patientsCheckedInToday',
            'patientsTreatmentCompletedToday',
            'normalAppointmentsToday',
            'normalAppointmentCount',
            'criticalAppointments',
            'criticalCount'
        ));
    }


    public function opdDiagnosisStats(Request $request)
    {
        $today = Carbon::today();
        $filterStatus = $request->query('status');
        $dateFilter = $request->query('date_filter', 'today');

        // วันที่ custom
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // เงื่อนไขเฉพาะแผนก OPD
        $baseQuery = MedicalDiagnosis::with('medicalReport.soldier')
            ->where('department_type', 'opd');

        // ✅ ยอดรวมทั้งหมด
        $totalStats = [
            'admit' => (clone $baseQuery)->where('treatment_status', 'Admit')->count(),
            'refer' => (clone $baseQuery)->where('treatment_status', 'Refer')->count(),
            'discharge' => (clone $baseQuery)->where('treatment_status', 'Discharge')->count(),
            'follow_up' => (clone $baseQuery)->where('treatment_status', 'Follow-up')->count(),
        ];




        // ✅ ยอดเฉพาะวันนี้หรือช่วงวันที่
        // ✅ สร้าง todayStats แยกจาก baseQuery โดยไม่แตะ date_filter จาก user
        $todayOnlyQuery = clone $baseQuery;
        $todayOnlyQuery->whereDate('diagnosis_date', $today);

        $todayStats = [
            'admit' => (clone $todayOnlyQuery)->where('treatment_status', 'Admit')->count(),
            'refer' => (clone $todayOnlyQuery)->where('treatment_status', 'Refer')->count(),
            'discharge' => (clone $todayOnlyQuery)->where('treatment_status', 'Discharge')->count(),
            'follow_up' => (clone $todayOnlyQuery)->where('treatment_status', 'Follow-up')->count(),
        ];

        // ✅ ส่วนนี้ใช้สำหรับ filter ตาราง
        $filteredQuery = clone $baseQuery;
        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $filteredQuery->whereBetween('diagnosis_date', [$startDate, $endDate . ' 23:59:59']);
        } elseif ($dateFilter === 'today') {
            $filteredQuery->whereDate('diagnosis_date', $today);
        }


        // ✅ ดึงข้อมูลการวินิจฉัยทั้งหมด (เฉพาะ opd)
        $diagnosisList = MedicalDiagnosis::with([
            'medicalReport',
            'medicalReport.soldier',
            'medicalReport.soldier.trainingUnit',
            'medicalReport.soldier.rotation'
        ])
            ->where('department_type', 'opd')
            ->orderByDesc('diagnosis_date')
            ->get();

        // ✅ รายละเอียดผู้ป่วยผ่าน DB::table แบบ LEFT JOIN เพื่อไม่ให้ข้อมูลหายหากบางตารางไม่มีข้อมูล
        $patientQuery = DB::table('medical_diagnosis as md')
            ->leftJoin('treatment as t', 'md.treatment_id', '=', 't.id')
            ->leftJoin('checkin as c', 't.checkin_id', '=', 'c.id')
            ->leftJoin('appointment as a', 'c.appointment_id', '=', 'a.id')
            ->leftJoin('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->leftJoin('soldier as s', 'mr.soldier_id', '=', 's.id')
            ->leftJoin('training_unit as tu', 's.training_unit_id', '=', 'tu.id')
            ->leftJoin('rotation as r', 's.rotation_id', '=', 'r.id')
            ->leftJoin('medical_diagnosis_diseases as mdd', 'md.id', '=', 'mdd.medical_diagnosis_id')
            ->leftJoin('icd10_diseases as icd', 'mdd.icd10_disease_id', '=', 'icd.id')
            ->where('md.department_type', 'opd')
            ->when($dateFilter === 'custom' && $startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('md.diagnosis_date', [$startDate, $endDate . ' 23:59:59']);
            })
            ->when($dateFilter === 'today', function ($query) use ($today) {
                return $query->whereDate('md.diagnosis_date', $today);
            })
            ->when($filterStatus && $filterStatus !== 'all', function ($query) use ($filterStatus) {
                return $query->where('md.treatment_status', $filterStatus);
            })
            ->select(
                's.first_name',
                's.last_name',
                's.soldier_id_card',
                'tu.unit_name as training_unit_name',
                'r.rotation_name as rotation_name',
                's.affiliated_unit',
                'md.treatment_status',
                DB::raw('GROUP_CONCAT(DISTINCT icd.icd10_code) as icd10_codes'),
                DB::raw('GROUP_CONCAT(DISTINCT icd.disease_name_en) as disease_names'),
                'md.diagnosis_date'
            )
            ->groupBy(
                's.first_name',
                's.last_name',
                's.soldier_id_card',
                'tu.unit_name',
                'r.rotation_name',
                's.affiliated_unit',
                'md.treatment_status',
                'md.diagnosis_date'
            )
            ->orderBy('md.diagnosis_date', 'desc');

        $patientDetails = $patientQuery->get();

        Log::info('✅ รวมทั้งหมด', $totalStats);
        Log::info('📅 เฉพาะวันนี้', $todayStats);
        Log::info('📋 Patient details', $patientDetails->toArray());

        return view('opd.history_opd', compact(
            'totalStats',
            'todayStats',
            'diagnosisList',
            'patientDetails'
        ));
    }


    public function viewTodayAppointment(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $filterStatus = $request->input('filter', 'all'); // checkin
        $filterCaseType = $request->input('case_type', 'all');
        $filterLocation = $request->input('location', 'all');

        $query = Appointment::with([
            'medicalReport.soldier',
            'medicalReport.soldier.trainingUnit',
            'medicalReport.soldier.rotation',
            'checkin',
            'checkin.treatment'  // เพิ่มการเชื่อมกับ treatment
        ])
            ->whereHas('medicalReport', function ($query) {
                $query->where('status', 'approved');
            })
            ->whereIn('status', ['scheduled', 'completed']) // รวมสถานะ scheduled และ completed
            ->whereDate('appointment_date', $today);
        // 🔹 กรอง case_type
        if ($filterCaseType !== 'all') {
            $query->where('case_type', $filterCaseType);
        }

        // 🔹 กรอง location (ยกเว้น ER ถ้าเลือก all)
        if ($filterLocation !== 'all') {
            $query->where('appointment_location', $filterLocation);
        } else {
            // exclude ER โดย default
            $query->where('appointment_location', '!=', 'ER');
        }

        // 🔹 กรองสถานะ checkin
        if ($filterStatus === 'checked-in') {
            $query->whereHas('checkin', function ($q) {
                $q->where('checkin_status', 'checked-in');
            });
        } elseif ($filterStatus === 'not-checked-in') {
            $query->whereDoesntHave('checkin')
                ->orWhereHas('checkin', function ($q) {
                    $q->where('checkin_status', '!=', 'checked-in');
                });
        }

        // ✅ เพิ่มสถานะลงไปใน object เพื่อใช้ใน view
        $appointments = $query->get()->map(function ($appointment) {
            // เช็คสถานะ checkin
            $appointment->checkin_status = $appointment->checkin && $appointment->checkin->checkin_status === 'checked-in'
                ? 'checked-in'
                : 'not-checked-in';

            // เพิ่มสถานะการรักษา (treated / not-treated)
            $appointment->treatment_status = $appointment->checkin && $appointment->checkin->treatment
                ? $appointment->checkin->treatment->treatment_status
                : 'not-treated';

            return $appointment;
        });

        return view('opd.scheduled_opd', compact(
            'appointments',
            'filterStatus',
            'filterCaseType',
            'filterLocation'
        ));
    }
    public function opdTodayAppointments(Request $request)
    {
        $caseType = $request->input('case_type');
        $rotationId = $request->input('rotation_id');
        $trainingUnitId = $request->input('training_unit_id');
        $todayStatus = $request->input('today_status');
        $appointmentDate = $request->input('appointment_date');
        $statusView = $request->input('status', 'today'); // สำหรับควบคุม UI

        // 📅 นัดหมายวันนี้
        $todayReports = MedicalReport::query()
            ->whereHas('appointment', function ($q) use ($caseType) {
                $q->whereDate('appointment_date', now()->toDateString())
                    ->whereIn('appointment_location', ['OPD', 'ARI clinic', 'กองทันตกรรม']);

                if ($caseType && $caseType !== 'all') {
                    $q->where('case_type', $caseType);
                }
            })
            ->when($rotationId, fn($q) => $q->whereHas('soldier.rotation', fn($q2) => $q2->where('id', $rotationId)))
            ->when($trainingUnitId, fn($q) => $q->whereHas('soldier', fn($q2) => $q2->where('training_unit_id', $trainingUnitId)))
            ->with(['appointment.checkin.treatment', 'soldier.trainingUnit', 'soldier.rotation'])
            ->get();

        // ✅ กรองตามสถานะในวันนี้
        if ($todayStatus && $todayStatus !== 'all') {
            $todayReports = $todayReports->filter(function ($report) use ($todayStatus) {
                $a = $report->appointment;
                $c = $a->checkin ?? null;
                $t = $c->treatment ?? null;

                $status = 'ไม่ทราบสถานะ';

                if ($a->status === 'scheduled' && optional($c)->checkin_status === 'not-checked-in') {
                    $status = 'ยังไม่ได้ทำการรักษา';
                } elseif ($a->status === 'scheduled' && optional($c)->checkin_status === 'checked-in' && optional($t)->treatment_status === 'not-treated') {
                    $status = 'อยู่ระหว่างการรักษา';
                } elseif ($a->status === 'completed' && optional($c)->checkin_status === 'checked-in' && optional($t)->treatment_status === 'treated') {
                    $status = 'รักษาสำเร็จ';
                } elseif ($a->status === 'missed') {
                    $status = 'ไม่มาตามนัด';
                }

                return $status === $todayStatus;
            });
        }

        // 🗓 นัดหมายทั้งหมด OPD + ตัวกรองวันที่
        $opdReports = MedicalReport::query()
            ->whereHas('appointment', function ($q) use ($caseType, $appointmentDate) {
                $q->whereIn('appointment_location', ['OPD', 'ARI clinic', 'กองทันตกรรม']);

                if ($caseType && $caseType !== 'all') {
                    $q->where('case_type', $caseType);
                }

                if ($appointmentDate) {
                    $q->whereDate('appointment_date', $appointmentDate);
                }
            })
            ->when($rotationId, fn($q) => $q->whereHas('soldier.rotation', fn($q2) => $q2->where('id', $rotationId)))
            ->when($trainingUnitId, fn($q) => $q->whereHas('soldier', fn($q2) => $q2->where('training_unit_id', $trainingUnitId)))
            ->with(['appointment', 'soldier.trainingUnit', 'soldier.rotation'])
            ->get();

        $rotations = Rotation::all();
        $trainingUnits = TrainingUnit::all();

        return view('opd.opd_appointments', compact(
            'todayReports',
            'opdReports',
            'rotations',
            'trainingUnits'
        ));
    }



}



