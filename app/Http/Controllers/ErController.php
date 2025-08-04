<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\MedicalDiagnosis;
use Carbon\Carbon;

use App\Models\Soldier;
use App\Models\MedicalReport;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\VitalSign;
use App\Models\Checkin;
use App\Models\Treatment;
use App\Models\ICD10Disease;
use Illuminate\Support\Facades\Log;

class ERController extends Controller
{
    public function showForm()
    {
        // ดึงข้อมูลที่จำเป็นหรืออาจไม่ต้องดึงก็ได้ (เช่น ทหารทั้งหมด หรือหน่วยต่างๆ)
        // แล้วส่งข้อมูลไปที่ View (ฟอร์มกรอกข้อมูลผู้ป่วย ER)
        return view('er.er_medical_report'); // เปลี่ยน 'er.form' เป็นชื่อ view ของคุณ
    }
    // ฟังก์ชันเพื่อดึงข้อมูลทหารจากชื่อ
    public function getByName(Request $request)
    {
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');

        $soldier = Soldier::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->with(['rotation', 'trainingUnit']) // ✅ โหลดความสัมพันธ์
            ->first();

        if ($soldier) {
            return response()->json([
                'success' => true,
                'soldier' => [
                    'soldier_id_card' => $soldier->soldier_id_card,
                    'rotation_name' => $soldier->rotation->rotation_name ?? 'ไม่ระบุ',
                    'training_unit_name' => $soldier->trainingUnit->unit_name ?? 'ไม่ระบุ',
                ]
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลทหาร']);
        }
    }

    // ฟังก์ชันสำหรับบันทึกข้อมูลผู้ป่วย ER
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูลที่ได้รับจากฟอร์ม
        $validatedData = $request->validate([
            'soldier_id_card' => 'required|string',
            'symptom_description' => 'required|string',
            'pain_score' => 'required|integer|min:1|max:10',
            'temperature' => 'required|numeric|min:30|max:45',
            'blood_pressure' => 'required|string',
            'heart_rate' => 'required|integer|min:40|max:180',
            'risk_level' => 'required|string', // ตรวจสอบว่ามีการกำหนดค่า
            'status' => 'required|string'  // ตรวจสอบสถานะ
        ]);

        // คำนวณระดับความเสี่ยง
        $riskLevel = $this->calculateRiskLevel($request);

        // บันทึกข้อมูล vital signs
        $vitalSigns = new VitalSign();
        $vitalSigns->temperature = $request->temperature;
        $vitalSigns->blood_pressure = $request->blood_pressure;
        $vitalSigns->heart_rate = $request->heart_rate;
        $vitalSigns->risk_level = $riskLevel;
        $vitalSigns->recorded_at = now(); // เวลาที่บันทึก
        $vitalSigns->save();

        // สร้างรายงานการแพทย์ใหม่
        $medicalReport = new MedicalReport();
        $medicalReport->soldier_id = Soldier::where('soldier_id_card', $request->soldier_id_card)->first()->id;
        $medicalReport->symptom_description = $request->symptom_description;
        $medicalReport->pain_score = $request->pain_score;  // pain_score เก็บใน medical_report
        $medicalReport->report_date = now();  // ใช้เวลาปัจจุบัน
        $medicalReport->status = 'in ER';  // ใช้สถานะที่ต้องการ
        $medicalReport->vital_signs_id = $vitalSigns->id; // เชื่อมโยงกับ vital_signs
        $medicalReport->save();


        // ถ้าสถานะเป็น "in ER", ตั้งค่านัดหมายให้ทันที
        if ($request->status == 'in ER') {
            $appointment = new Appointment();
            $appointment->medical_report_id = $medicalReport->id;
            $appointment->appointment_date = now();  // ใช้เวลาปัจจุบัน
            $appointment->status = 'scheduled'; // รอการยืนยัน
            $appointment->appointment_location = 'ER'; // ตั้งค่า location เป็น "ER"
            $appointment->save();
        }

        $checkin = new Checkin();
        $checkin->appointment_id = $appointment->id; // เชื่อมโยงกับ Appointment
        $checkin->checkin_status = 'checked-in'; // ตั้งสถานะเป็น "checked-in"
        $checkin->checkin_time = now(); // เวลาที่เช็คอิน
        $checkin->save();
        // สร้างข้อมูล tmtreaent ใหม่
        $treatment = new Treatment();
        $treatment->checkin_id = $checkin->id;
        $treatment->treatment_date = now();
        $treatment->treatment_status = 'treated';
        $treatment->save();
        return response()->json([
            'success' => true,
            'message' => 'บันทึกข้อมูลผู้ป่วย ER สำเร็จ',
            'redirect' => route('er.form') // เปลี่ยนเส้นทางหลังบันทึก
        ]);
    }

    public function calculateRiskLevel(Request $request)
    {
        $temperature = $request->temperature;
        $bp = explode('/', $request->blood_pressure);
        $systolic = (int) $bp[0];
        $diastolic = (int) $bp[1];
        $heartRate = (int) $request->heart_rate;

        // คำนวณระดับความเสี่ยง
        if ($temperature > 40) {
            return 'red';
        } elseif ($temperature > 38) {
            return 'yellow';
        } else {
            if ($systolic >= 180 || $diastolic >= 120) {
                return 'red';
            } elseif ($systolic >= 140 || $diastolic >= 90) {
                return 'red';
            } elseif ($systolic >= 121 || $diastolic >= 81) {
                return 'yellow';
            } elseif ($systolic < 90 || $diastolic < 60) {
                return 'yellow';
            } else {
                return 'green';
            }
        }
    }

    public function getPatientsInER()
    {
        $appointments = Appointment::with(['medicalReport.soldier', 'checkin.treatment'])
            ->whereDate('appointment_date', now()) // ดึงเฉพาะวันนี้
            ->where('appointment_location', 'ER')  // เฉพาะ ER
            ->where('status', '!=', 'completed')   // ❗ กรองไม่ให้ดึงนัดที่เสร็จแล้ว
            ->whereHas('medicalReport', function ($query) {
                $query->where('status', 'in ER');  // ต้องยังอยู่ใน ER
            })
            ->whereHas('checkin', function ($query) {
                $query->where('checkin_status', 'checked-in'); // ต้องเช็คอินแล้ว
            })
            ->get();

        return view('er.er_treatment', compact('appointments'));
    }


    public function showDiagnosisForm($treatmentId)
    {
        // ใช้ $treatmentId จาก URL
        $treatment = Treatment::with('checkin.appointment.medicalReport.soldier', 'checkin.appointment.medicalReport.vitalSign')
            ->find($treatmentId);

        if ($treatment && $treatment->checkin && $treatment->checkin->appointment && $treatment->checkin->appointment->medicalReport) {
            $soldier = $treatment->checkin->appointment->medicalReport->soldier;
            $soldierName = $soldier->first_name . ' ' . $soldier->last_name;
            $soldierUnit = $soldier->affiliated_unit ?? 'ไม่ระบุ';
            $soldierRotation = $soldier->rotation_id ?? 'ไม่ระบุ';
            $soldierTraining = $soldier->training_unit_id ?? 'ไม่ระบุ';

            $vitalSign = $treatment->checkin->appointment->medicalReport->vitalSign;
            $temperature = $vitalSign->temperature ?? '-';
            $bloodPressure = $vitalSign->blood_pressure ?? '-';
            $heartRate = $vitalSign->heart_rate ?? '-';
        } else {
            $soldierName = 'ไม่พบข้อมูลทหาร';
            $soldierUnit = $soldierRotation = $soldierTraining = 'ไม่พบข้อมูล';
            $temperature = $bloodPressure = $heartRate = '-';
        }

        return view('er.er_diagnosis_form', compact(
            'soldierName',
            'soldierUnit',
            'soldierRotation',
            'soldierTraining',
            'temperature',
            'bloodPressure',
            'heartRate',
            'treatmentId'
        ));
    }






    public function dashboardEr()
    {
        // ดึงข้อมูลสถิติ
        $stats = DB::table('appointment as a')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->join('vital_signs as vs', 'mr.vital_signs_id', '=', 'vs.id')
            ->join('soldier as s', 'mr.soldier_id', '=', 's.id')
            ->join('checkin as c', 'a.id', '=', 'c.appointment_id')
            ->join('treatment as t', 'c.id', '=', 't.checkin_id')
            ->select(
                DB::raw('COUNT(DISTINCT a.id) as appointment_today_count'),  // นับจำนวนการนัดหมายวันนี้
                DB::raw('COUNT(CASE WHEN vs.risk_level = "red" THEN 1 END) as red_count'),  // จำนวนสีแดง
                DB::raw('COUNT(CASE WHEN vs.risk_level = "yellow" THEN 1 END) as yellow_count'),  // จำนวนสีเหลือง
                DB::raw('COUNT(CASE WHEN vs.risk_level = "green" THEN 1 END) as green_count'),  // จำนวนสีเขียว
                DB::raw('COUNT(CASE WHEN t.treatment_status = "not-treated" THEN 1 END) as not_treated_count'),  // จำนวนผู้ป่วยที่ยังไม่ได้รับการรักษา
                DB::raw('COUNT(CASE WHEN t.treatment_status = "treated" THEN 1 END) as treated_count'),  // จำนวนผู้ป่วยที่รักษาเสร็จแล้ว
                DB::raw('COUNT(CASE WHEN a.status = "completed" AND mr.status = "in ER" THEN 1 END) as completed_in_er_count') // จำนวน appointments ที่เป็น completed และ medical_report เป็น in ER
            )
            ->whereDate('a.appointment_date', '=', now()->toDateString())
            ->where('mr.status', '=', 'in ER')
            ->whereNotNull('vs.risk_level')
            ->first();

        // ดึงข้อมูลรายการนัดหมายทั้งหมด + ข้อมูลหน่วยฝึกและผลัด
        $appointments = DB::table('appointment as a')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->join('vital_signs as vs', 'mr.vital_signs_id', '=', 'vs.id')
            ->join('soldier as s', 'mr.soldier_id', '=', 's.id')
            ->join('checkin as c', 'a.id', '=', 'c.appointment_id')
            ->join('treatment as t', 'c.id', '=', 't.checkin_id')
            ->leftJoin('training_unit as tu', 's.training_unit_id', '=', 'tu.id')
            ->leftJoin('rotation as r', 's.rotation_id', '=', 'r.id')
            ->select(
                's.soldier_id_card', // เพิ่มเลขบัตรประชาชนทหาร
                's.first_name',
                's.last_name',
                'vs.risk_level',
                't.treatment_status',
                'a.appointment_date',
                'a.appointment_location',
                'tu.unit_name as training_unit_name',
                'r.rotation_name as rotation_name'
            )
            ->whereDate('a.appointment_date', '=', now()->toDateString())
            ->where('mr.status', '=', 'in ER')
            ->whereNotNull('vs.risk_level')
            ->orderByRaw("FIELD(treatment_status, 'not-treated', 'treated')") // จัดลำดับสถานะการรักษา
            ->orderByRaw("FIELD(risk_level, 'red', 'yellow', 'green')") // จัดลำดับระดับความเสี่ยง
            ->take(3)  // จำกัดให้แสดงแค่ 3 รายการ
            ->get();  // ดึงข้อมูลทั้งหมดที่ตรงเงื่อนไข

        return view('er.dashboard_er', [
            'appointment_today_count' => $stats->appointment_today_count,
            'red_count' => $stats->red_count,
            'yellow_count' => $stats->yellow_count,
            'green_count' => $stats->green_count,
            'not_treated_count' => $stats->not_treated_count,
            'treated_count' => $stats->treated_count,
            'completed_in_er_count' => $stats->completed_in_er_count,  // ส่งข้อมูลจำนวน completed ที่เป็น in ER
            'appointments' => $appointments
        ]);
    }


    public function erDiagnosisStats(Request $request)
    {
        $today = Carbon::today();
        $filterStatus = $request->query('status');
        $dateFilter = $request->query('date_filter', 'today');

        // วันที่ custom
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // เงื่อนไขเฉพาะแผนก ER (เปลี่ยนจาก 'opd' เป็น 'er')
        $baseQuery = MedicalDiagnosis::with('medicalReport.soldier')
            ->where('department_type', 'er'); // เปลี่ยนเป็น 'er'

        // ✅ ยอดรวมทั้งหมด
        $totalStats = [
            'admit' => (clone $baseQuery)->where('treatment_status', 'Admit')->count(),
            'refer' => (clone $baseQuery)->where('treatment_status', 'Refer')->count(),
            'discharge' => (clone $baseQuery)->where('treatment_status', 'Discharge')->count(), // เปลี่ยนจาก 'discharge_up'
            'follow_up' => (clone $baseQuery)->where('treatment_status', 'Follow-up')->count(),
        ];

        // ✅ ยอดเฉพาะวันนี้หรือช่วงวันที่
        $filteredQuery = clone $baseQuery;
        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $filteredQuery->whereBetween('diagnosis_date', [$startDate, $endDate . ' 23:59:59']);
        } elseif ($dateFilter === 'today') {
            $filteredQuery->whereDate('diagnosis_date', $today);
        }

        $todayStats = [
            'admit' => (clone $filteredQuery)->where('treatment_status', 'Admit')->count(),
            'refer' => (clone $filteredQuery)->where('treatment_status', 'Refer')->count(),
            'discharge' => (clone $filteredQuery)->where('treatment_status', 'Discharge')->count(), // เปลี่ยนจาก 'discharge_up'
            'follow_up' => (clone $filteredQuery)->where('treatment_status', 'Follow-up')->count(),
        ];

        // ✅ ดึงข้อมูลการวินิจฉัยทั้งหมด (เฉพาะ er)
        $diagnosisList = MedicalDiagnosis::with([
            'medicalReport',
            'medicalReport.soldier',
            'medicalReport.soldier.trainingUnit',
            'medicalReport.soldier.rotation'
        ])
            ->where('department_type', 'er') // เปลี่ยนเป็น 'er'
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
            ->where('md.department_type', 'er') // เปลี่ยนเป็น 'er'
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
                's.affiliated_unit',
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

        \Log::info('✅ รวมทั้งหมด', $totalStats);
        \Log::info('📅 เฉพาะวันนี้', $todayStats);
        \Log::info('📋 Patient details', $patientDetails->toArray());

        return view('er.history_er', compact(
            'totalStats',
            'todayStats',
            'diagnosisList',
            'patientDetails'
        ));
    }




    public function viewTodayAppointment(Request $request)
    {
        // ดึงข้อมูลเริ่มต้นสำหรับแสดงในหน้า
        $data = $this->getAppointmentData($request);

        // Return view สำหรับ Web
        return view('er.scheduled_er', $data);
    }

    /**
     * 📱 API Endpoint สำหรับ /er/appointments
     */
    public function apiTodayAppointment(Request $request)
    {
        try {
            $data = $this->getAppointmentData($request);

            return response()->json([
                'success' => true,
                'data' => [
                    'appointments' => $data['appointments'],
                    'statistics' => $data['statistics'],
                    'filters' => [
                        'status' => $data['filterStatus'],
                        'case_type' => $data['filterCaseType'],
                        'location' => $data['filterLocation'],
                        'treatment_status' => $data['filterTreatmentStatus'],
                        'risk_level' => $data['filterRiskLevel'],
                        'date_filter' => $data['dateFilter'],
                        'specific_date' => $data['specificDate'],
                        'start_date' => $data['startDate'],
                        'end_date' => $data['endDate']
                    ]
                ],
                'message' => 'Appointments retrieved successfully',
                'timestamp' => now()->toISOString(),
                'total_records' => $data['appointments']->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving appointments: ' . $e->getMessage(),
                'error_code' => 'APPOINTMENT_RETRIEVAL_ERROR',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * 🔄 Core Logic Function (ใช้ร่วมกันระหว่าง View และ API)
     */
    private function getAppointmentData(Request $request)
    {
        // 🗓️ การกรองวันที่
        $dateFilter = $request->input('date_filter', 'today');
        $specificDate = $request->input('specific_date');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // กำหนดวันที่ตามประเภทฟิลเตอร์
        switch ($dateFilter) {
            case 'specific_date':
                $targetDate = $specificDate ? Carbon::parse($specificDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
                break;
            case 'date_range':
                $startDate = $startDate ? Carbon::parse($startDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
                $endDate = $endDate ? Carbon::parse($endDate)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
                break;
            case 'today':
            default:
                $targetDate = Carbon::today()->format('Y-m-d');
        }

        // ฟิลเตอร์เดิม
        $filterStatus = $request->input('filter', 'all');
        $filterCaseType = $request->input('case_type', 'all');
        $filterLocation = $request->input('location', 'all');
        $filterTreatmentStatus = $request->input('treatment_status', 'all');
        $filterRiskLevel = $request->input('risk_level', 'all');

        // เริ่มต้น query สำหรับการนัดหมาย - ใช้ Relationship เดิมที่มีอยู่แล้ว
        $query = Appointment::with([
            'medicalReport.soldier',
            'medicalReport.soldier.trainingUnit',
            'medicalReport.soldier.rotation',
            'checkin',
            'checkin.treatment',
            'medicalReport.vitalSign'
        ])
            ->whereHas('medicalReport', function ($query) {
                $query->where('status', 'in ER');
            })
            ->whereIn('status', ['scheduled', 'completed']);

        // 🗓️ กรองตามวันที่
        if ($dateFilter === 'date_range') {
            $query->whereBetween('appointment_date', [$startDate, $endDate]);
        } elseif (isset($targetDate)) {
            $query->whereDate('appointment_date', $targetDate);
        }

        // กรองข้อมูลต่างๆ
        if ($filterCaseType !== 'all') {
            $query->where('case_type', $filterCaseType);
        }

        if ($filterLocation !== 'all') {
            $query->where('appointment_location', $filterLocation);
        }

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

        if ($filterTreatmentStatus !== 'all') {
            $query->whereHas('checkin.treatment', function ($q) use ($filterTreatmentStatus) {
                $q->where('treatment_status', $filterTreatmentStatus);
            });
        }

        if ($filterRiskLevel !== 'all') {
            $query->whereHas('medicalReport.vitalSign', function ($q) use ($filterRiskLevel) {
                $q->where('risk_level', $filterRiskLevel);
            });
        }

        // ดึงข้อมูลและประมวลผล
        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($appointment) {
                // กำหนดสถานะ check-in
                $appointment->checkin_status = $appointment->checkin && $appointment->checkin->checkin_status === 'checked-in'
                    ? 'checked-in'
                    : 'not-checked-in';

                // กำหนดสถานะการรักษา
                $appointment->treatment_status = $appointment->checkin && $appointment->checkin->treatment
                    ? $appointment->checkin->treatment->treatment_status
                    : 'not-treated';

                // กำหนดระดับความเสี่ยง
                $appointment->risk_level = $appointment->medicalReport && $appointment->medicalReport->vitalSign
                    ? $appointment->medicalReport->vitalSign->risk_level
                    : 'not-assessed';

                if ($appointment->medicalReport && $appointment->medicalReport->vitalSign) {
                    $vitalSign = $appointment->medicalReport->vitalSign;

                    $appointment->temperature = $vitalSign->temperature ?? null;
                    $appointment->blood_pressure = $vitalSign->blood_pressure ?? 'ไม่ระบุ';
                    $appointment->heart_rate = $vitalSign->heart_rate ?? null;
                    $appointment->vital_signs_recorded_at = $vitalSign->recorded_at ?? null;
                    $appointment->vital_signs_source = $vitalSign->source ?? 'ไม่ระบุ';
                } else {
                    // กรณีไม่มีข้อมูลสัญญาณชีพ
                    $appointment->temperature = null;
                    $appointment->blood_pressure = 'ไม่ระบุ';
                    $appointment->heart_rate = null;
                    $appointment->vital_signs_recorded_at = null;
                    $appointment->vital_signs_source = 'ไม่ระบุ';
                }

                // 🖼️ จัดการรูปภาพทหาร - สร้าง URL แทน base64
                // แทนที่ส่วนการจัดการรูปภาพทหารใน Controller
// 🖼️ จัดการรูปภาพทหาร - ใช้ path ที่ถูกต้อง (uploads/soldiers)
                if ($appointment->medicalReport && $appointment->medicalReport->soldier && $appointment->medicalReport->soldier->soldier_image) {
                    $soldierImage = $appointment->medicalReport->soldier->soldier_image;

                    // วิธีที่ปลอดภัย: ใช้ regex ดึงแค่ชื่อไฟล์
                    if (preg_match('/([^\/]+\.(jpg|jpeg|png|gif|webp))$/i', $soldierImage, $matches)) {
                        $filename = $matches[1]; // เช่น 1741470721.jpg
                        // ใช้ uploads/soldiers แทน storage/soldiers
                        $appointment->soldier_image_url = asset('uploads/soldiers/' . $filename);
                    } else {
                        // ถ้าไม่เจอไฟล์ในรูปแบบที่คาดหวัง
                        $appointment->soldier_image_url = null;
                    }

                    // Debug log (ลบออกได้หลังจากแก้ไขเสร็จ)
                    \Log::info('Image processing - Original: ' . $soldierImage);
                    \Log::info('Image processing - Final: ' . ($appointment->soldier_image_url ?? 'null'));

                } else {
                    $appointment->soldier_image_url = null;
                }

                // เก็บ soldier_image_base64 เป็น null
                $appointment->soldier_image_base64 = null;

                // เพิ่มสถานที่นัดหมาย
                $appointment->appointment_location = $appointment->appointment_location ?? 'ไม่ระบุ';            // เพิ่มรหัสบัตรประชาชน
                $appointment->soldier_id_card = $appointment->medicalReport && $appointment->medicalReport->soldier
                    ? $appointment->medicalReport->soldier->soldier_id_card
                    : null;

                // ✅ แก้ไขชื่อ field ให้ตรงกับ database
                if ($appointment->medicalReport && $appointment->medicalReport->soldier) {
                    $soldier = $appointment->medicalReport->soldier;

                    // ใช้ first_name และ last_name แทน soldier_fname และ soldier_lname
                    $firstName = $soldier->first_name ?? '';
                    $lastName = $soldier->last_name ?? '';

                    $appointment->soldier_name = trim($firstName . ' ' . $lastName) ?: 'ไม่ระบุ';
                } else {
                    $appointment->soldier_name = 'ไม่ระบุ';
                }

                // เพิ่มข้อมูลหน่วยและผลัด
                $appointment->training_unit = $appointment->medicalReport
                    && $appointment->medicalReport->soldier
                    && $appointment->medicalReport->soldier->trainingUnit
                    ? $appointment->medicalReport->soldier->trainingUnit->unit_name
                    : 'ไม่ระบุ';

                $appointment->rotation = $appointment->medicalReport
                    && $appointment->medicalReport->soldier
                    && $appointment->medicalReport->soldier->rotation
                    ? $appointment->medicalReport->soldier->rotation->rotation_name
                    : 'ไม่ระบุ';

                $appointment->affiliated_unit = $appointment->medicalReport
                    && $appointment->medicalReport->soldier
                    && $appointment->medicalReport->soldier->affiliated_unit
                    ? $appointment->medicalReport->soldier->affiliated_unit
                    : 'ไม่ระบุ';

                // ✅ เพิ่มข้อมูลการวินิจฉัยและโรค - ใช้ DB::table
                $this->processMedicalDiagnosisData($appointment);

                return $appointment;
            });

        // 📊 สถิติเพิ่มเติม
        $statistics = [
            'total_appointments' => $appointments->count(),
            'checked_in' => $appointments->where('checkin_status', 'checked-in')->count(),
            'not_checked_in' => $appointments->where('checkin_status', 'not-checked-in')->count(),
            'treated' => $appointments->where('treatment_status', 'treated')->count(),
            'not_treated' => $appointments->where('treatment_status', 'not-treated')->count(),
            'risk_red' => $appointments->where('risk_level', 'red')->count(),
            'risk_yellow' => $appointments->where('risk_level', 'yellow')->count(),
            'risk_green' => $appointments->where('risk_level', 'green')->count(),
            'risk_not_assessed' => $appointments->where('risk_level', 'not-assessed')->count(),
            // ✅ เพิ่มสถิติการวินิจฉัย
            'diagnosed' => $appointments->where('disease_list', '!=', 'ไม่ระบุ')->count(),
            'not_diagnosed' => $appointments->where('disease_list', 'ไม่ระบุ')->count(),
            'admit_status' => $appointments->where('diagnosis_treatment_status', 'รับไว้รักษา')->count(),
            'refer_status' => $appointments->where('diagnosis_treatment_status', 'ส่งต่อ')->count(),
            'discharge_status' => $appointments->where('diagnosis_treatment_status', 'จำหน่าย')->count(),
            'followup_status' => $appointments->where('diagnosis_treatment_status', 'นัดติดตาม')->count(),
        ];

        return compact(
            'appointments',
            'statistics',
            'filterStatus',
            'filterCaseType',
            'filterLocation',
            'filterTreatmentStatus',
            'filterRiskLevel',
            'dateFilter',
            'specificDate',
            'startDate',
            'endDate'
        );
    }

    /**
     * 🏥 ประมวลผลข้อมูลการวินิจฉัย - ใช้ DB::table แทน Eloquent
     */
    private function processMedicalDiagnosisData($appointment)
    {
        $medicalDiagnosis = null;

        // ดึงข้อมูลจากตาราง medical_diagnosis โดยใช้ treatment_id
        if ($appointment->checkin && $appointment->checkin->treatment) {
            $medicalDiagnosis = DB::table('medical_diagnosis')
                ->where('treatment_id', $appointment->checkin->treatment->id)
                ->first();
        }

        if ($medicalDiagnosis) {
            // ข้อมูลแพทย์และการรักษา
            $appointment->doctor_name = $medicalDiagnosis->doctor_name ?? 'ไม่ระบุ';
            $appointment->diagnosis_treatment_status = $this->translateTreatmentStatus($medicalDiagnosis->treatment_status);
            $appointment->training_instruction = $medicalDiagnosis->training_instruction ?? 'ไม่ระบุ';
            $appointment->diagnosis_date = $medicalDiagnosis->diagnosis_date ?? null;
            $appointment->diagnosis_notes = $medicalDiagnosis->notes ?? 'ไม่ระบุ';

            // ดึงข้อมูลโรคจาก junction table
            $diseases = DB::table('medical_diagnosis_diseases as mdd')
                ->join('icd10_diseases as icd', 'mdd.icd10_disease_id', '=', 'icd.id')
                ->where('mdd.medical_diagnosis_id', $medicalDiagnosis->id)
                ->select('icd.icd10_code', 'icd.disease_name_en', 'icd.level')
                ->get()
                ->map(function ($disease) {
                    return [
                        'icd10_code' => $disease->icd10_code,
                        'disease_name' => $disease->disease_name_en,
                        'level' => $disease->level
                    ];
                })
                ->toArray();

            $appointment->diseases = $diseases;

            // สร้างรายการโรคแบบข้อความ
            $diseaseNames = collect($diseases)->pluck('disease_name')->toArray();
            $appointment->disease_list = !empty($diseaseNames) ? implode(', ', $diseaseNames) : 'ไม่ระบุ';

            // สร้างรายการรหัสโรค
            $diseaseCodes = collect($diseases)->pluck('icd10_code')->toArray();
            $appointment->disease_codes = !empty($diseaseCodes) ? implode(', ', $diseaseCodes) : 'ไม่ระบุ';

        } else {
            // กรณีไม่มีข้อมูลการวินิจฉัย
            $appointment->doctor_name = 'ไม่ระบุ';
            $appointment->diagnosis_treatment_status = 'ไม่ระบุ';
            $appointment->training_instruction = 'ไม่ระบุ';
            $appointment->diagnosis_date = null;
            $appointment->diagnosis_notes = 'ไม่ระบุ';
            $appointment->diseases = [];
            $appointment->disease_list = 'ไม่ระบุ';
            $appointment->disease_codes = 'ไม่ระบุ';
        }
    }

    /**
     * 🔄 แปลงสถานะการรักษาเป็นภาษาไทย
     */
    private function translateTreatmentStatus($status)
    {
        $statusMap = [
            'Admit' => 'Admit',
            'Refer' => 'Refer',
            'Discharge' => 'Discharge',
            'Follow-up' => 'Follow-up',
            'Followup' => 'นัดติดตาม'
        ];

        return $statusMap[$status] ?? 'ไม่ระบุ';
    }

    /**
     * 📊 ฟังก์ชันสำหรับดูผู้ป่วยความเสี่ยงสูง
     */
    public function getHighRiskPatients(Request $request)
    {
        $request->merge(['risk_level' => 'red']);
        return $this->apiTodayAppointment($request);
    }

    /**
     * 👥 ฟังก์ชันสำหรับดูผู้ป่วยที่เช็คอินแล้ว
     */
    public function getCheckedInPatients(Request $request)
    {
        $request->merge(['filter' => 'checked-in']);
        return $this->apiTodayAppointment($request);
    }

    /**
     * 🏥 ฟังก์ชันสำหรับดูผู้ป่วยที่ได้รับการวินิจฉัยแล้ว
     */
    public function getDiagnosedPatients(Request $request)
    {
        try {
            $data = $this->getAppointmentData($request);

            // กรองเฉพาะผู้ป่วยที่ได้รับการวินิจฉัยแล้ว
            $diagnosedAppointments = $data['appointments']->filter(function ($appointment) {
                return $appointment->doctor_name !== 'ไม่ระบุ' &&
                    $appointment->disease_list !== 'ไม่ระบุ';
            });

            // คำนวณสถิติใหม่
            $diagnosedStatistics = [
                'total_diagnosed' => $diagnosedAppointments->count(),
                'admit_count' => $diagnosedAppointments->where('diagnosis_treatment_status', 'รับไว้รักษา')->count(),
                'refer_count' => $diagnosedAppointments->where('diagnosis_treatment_status', 'ส่งต่อ')->count(),
                'discharge_count' => $diagnosedAppointments->where('diagnosis_treatment_status', 'จำหน่าย')->count(),
                'followup_count' => $diagnosedAppointments->where('diagnosis_treatment_status', 'นัดติดตาม')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'appointments' => $diagnosedAppointments->values(),
                    'statistics' => array_merge($data['statistics'], $diagnosedStatistics)
                ],
                'message' => 'Diagnosed patients retrieved successfully',
                'timestamp' => now()->toISOString(),
                'total_records' => $diagnosedAppointments->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving diagnosed patients: ' . $e->getMessage(),
                'error_code' => 'DIAGNOSED_PATIENTS_ERROR',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * 🏥 ฟังก์ชันสำหรับดูผู้ป่วยที่รับไว้รักษา
     */
    public function getAdmittedPatients(Request $request)
    {
        try {
            $data = $this->getAppointmentData($request);

            // กรองเฉพาะผู้ป่วยที่รับไว้รักษา
            $admittedAppointments = $data['appointments']->filter(function ($appointment) {
                return $appointment->diagnosis_treatment_status === 'รับไว้รักษา';
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'appointments' => $admittedAppointments->values(),
                    'statistics' => $data['statistics']
                ],
                'message' => 'Admitted patients retrieved successfully',
                'timestamp' => now()->toISOString(),
                'total_records' => $admittedAppointments->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving admitted patients: ' . $e->getMessage(),
                'error_code' => 'ADMITTED_PATIENTS_ERROR',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * 📈 ฟังก์ชันสำหรับดูสถิติการวินิจฉัย
     */
    public function getDiagnosisStatistics(Request $request)
    {
        try {
            $data = $this->getAppointmentData($request);
            $appointments = $data['appointments'];

            // สถิติการวินิจฉัยแบบละเอียด
            $detailedStatistics = [
                'overview' => [
                    'total_patients' => $appointments->count(),
                    'diagnosed_patients' => $appointments->where('disease_list', '!=', 'ไม่ระบุ')->count(),
                    'not_diagnosed_patients' => $appointments->where('disease_list', 'ไม่ระบุ')->count(),
                ],
                'treatment_status' => [
                    'admit' => $appointments->where('diagnosis_treatment_status', 'รับไว้รักษา')->count(),
                    'refer' => $appointments->where('diagnosis_treatment_status', 'ส่งต่อ')->count(),
                    'discharge' => $appointments->where('diagnosis_treatment_status', 'จำหน่าย')->count(),
                    'followup' => $appointments->where('diagnosis_treatment_status', 'นัดติดตาม')->count(),
                ],
                'doctors' => $appointments->where('doctor_name', '!=', 'ไม่ระบุ')
                    ->groupBy('doctor_name')
                    ->map(function ($group) {
                        return $group->count();
                    }),
                'common_diseases' => $appointments->where('disease_list', '!=', 'ไม่ระบุ')
                    ->flatMap(function ($appointment) {
                        return $appointment->diseases;
                    })
                    ->groupBy('disease_name')
                    ->map(function ($group) {
                        return $group->count();
                    })
                    ->sortDesc()
                    ->take(10)
            ];

            return response()->json([
                'success' => true,
                'data' => $detailedStatistics,
                'message' => 'Diagnosis statistics retrieved successfully',
                'timestamp' => now()->toISOString()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving diagnosis statistics: ' . $e->getMessage(),
                'error_code' => 'DIAGNOSIS_STATISTICS_ERROR',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
    public function storeWithDiagnosis(Request $request)
    {
        DB::beginTransaction();

        try {

            // ค้นหาทหาร
            $soldier = Soldier::where('soldier_id_card', $request->soldier_id_card)->firstOrFail();

            //  บันทึก Vital Signs
            $vitalSigns = VitalSign::create([
                'temperature' => $request->temperature,
                'blood_pressure' => $request->blood_pressure,
                'heart_rate' => $request->heart_rate,
                'risk_level' => $this->calculateRiskLevel($request),
                'recorded_at' => now(),
            ]);

            //  สร้าง Medical Report
            $medicalReport = MedicalReport::create([
                'soldier_id' => $soldier->id,
                'symptom_description' => $request->symptom_description,
                'pain_score' => $request->pain_score,
                'report_date' => now(),
                'status' => 'in ER',
                'vital_signs_id' => $vitalSigns->id,
            ]);

            //  สร้าง Appointment และ Checkin
            $appointment = Appointment::create([
                'medical_report_id' => $medicalReport->id,
                'appointment_date' => now(),
                'status' => 'scheduled',
                'appointment_location' => 'ER',
            ]);

            $checkin = Checkin::create([
                'appointment_id' => $appointment->id,
                'checkin_status' => 'checked-in',
                'checkin_time' => now(),
            ]);

            // สร้าง Treatment
            $treatment = Treatment::create([
                'checkin_id' => $checkin->id,
                'treatment_date' => now(),
                'treatment_status' => 'treated',
            ]);

            // สร้าง Diagnosis
            $diagnosis = MedicalDiagnosis::create([
                'treatment_id' => $treatment->id,
                'doctor_name' => $request->doctor_name,
                'treatment_status' => $request->treatment_status,
                'department_type' => 'er',
                'vital_signs_id' => $vitalSigns->id,
                'diagnosis_date' => now(),
                'notes' => $request->notes,
                'training_instruction' => $request->input('training_instruction'), // ✅ ใส่ตรงนี้
            ]);

            // เชื่อม ICD10
            $icdCodes = explode(',', $request->icd10_code);
            $diseaseIds = ICD10Disease::whereIn('icd10_code', $icdCodes)->pluck('id');
            $diagnosis->diseases()->attach($diseaseIds);

            if (
                in_array($request->treatment_status, ['Admit', 'Refer', 'Discharge', 'Follow-up'])
            ) {
                $appointment->update(['status' => 'completed']);
                $treatment->update(['treatment_status' => 'treated']);
            }

            // ✅ ถ้าเป็น Admit → สร้างใหม่สำหรับ IPD
            $oldMedicalReport = $treatment->checkin->appointment->medicalReport ?? null;

            switch ($request->treatment_status) {
                case 'Admit':
                    $admitDiagnosis = MedicalDiagnosis::create([
                        'treatment_id' => $treatment->id,
                        'doctor_name' => $request->doctor_name,
                        'treatment_status' => 'Admit',
                        'department_type' => 'ipd',
                        'vital_signs_id' => $vitalSigns->id,
                        'diagnosis_date' => now(),
                        'notes' => $request->notes
                    ]);
                    $admitDiagnosis->diseases()->attach($diseaseIds);
                    break;

                case 'Follow-up':
                    if (!$oldMedicalReport) {
                        Log::info('ไม่พบข้อมูล oldMedicalReport');
                        return response()->json(['message' => 'ไม่พบข้อมูล medical_report เก่า'], 404);
                    }

                    Log::info('Old Medical Report ID: ' . $oldMedicalReport->id);

                    // สร้าง Medical Report ใหม่สำหรับ Follow-up
                    $newMedicalReport = MedicalReport::create([
                        'soldier_id' => $soldier->id,
                        'symptom_description' => 'นัดติดตามอาการ',
                        'status' => 'approved',
                        'report_date' => now(),
                        'previous_report_id' => $oldMedicalReport->id,
                    ]);

                    Log::info('New Medical Report previous_report_id (after creation): ' . $newMedicalReport->previous_report_id);

                    // สร้าง VitalSign ว่างสำหรับติดตาม
                    $newVitalSign = VitalSign::create([
                        'temperature' => null,
                        'blood_pressure' => null,
                        'heart_rate' => null,
                        'source' => 'appointment',
                        'risk_level' => null,
                    ]);

                    $newMedicalReport->update(['vital_signs_id' => $newVitalSign->id]);

                    // สร้าง Appointment สำหรับ Follow-up
                    $newAppointment = Appointment::create([
                        'medical_report_id' => $newMedicalReport->id,
                        'appointment_date' => $request->appointment_date,
                        'appointment_location' => $request->appointment_location,
                        'case_type' => $request->case_type,
                        'status' => 'scheduled',
                        'is_follow_up' => 1,
                    ]);
                    $newMedicalReport->update(['appointment_id' => $newAppointment->id]);

                    // ✅ เพิ่ม: สร้าง Checkin หลังจากสร้าง Appointment
                    $checkin = Checkin::create([
                        'appointment_id' => $newAppointment->id,
                        'checkin_status' => 'not-checked-in',
                        'checkin_time' => null,
                    ]);
                    // ✅ ถ้า checkin_status = 'checked-in' ให้สร้าง Treatment ทันที
                    if ($checkin->checkin_status === 'checked-in') {
                        $treatment = Treatment::create([
                            'checkin_id' => $checkin->id,
                            'treatment_date' => now(),
                            'treatment_status' => 'not-treated',
                        ]);

                        Log::info('สร้าง Treatment ทันทีหลังจาก Check-in', [
                            'treatment_id' => $treatment->id,
                            'checkin_id' => $checkin->id
                        ]);
                    }
                    // ❗ ไม่ต้อง return ที่นี่ ถ้ายังอยู่ใน logic หลักของ flow ใหญ่
                    break;
            }

            DB::commit();

            return redirect()->route('er.today')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('er.today')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }





}