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
class ERController extends Controller
{
    public function showForm()
    {
        // ดึงข้อมูลที่จำเป็นหรืออาจไม่ต้องดึงก็ได้ (เช่น ทหารทั้งหมด หรือหน่วยต่างๆ)
        // แล้วส่งข้อมูลไปที่ View (ฟอร์มกรอกข้อมูลผู้ป่วย ER)
        return view('er.er_medical_report'); // เปลี่ยน 'er.form' เป็นชื่อ view ของคุณ
    }
    // ฟังก์ชันเพื่อดึงข้อมูลทหารจากเลขบัตรประชาชน
    public function getByIdCard(Request $request)
    {
        // ค้นหาทหารจากเลขบัตรประชาชน
        $soldier = Soldier::where('soldier_id_card', $request->id_card)->first();

        if ($soldier) {
            return response()->json(['success' => true, 'soldier' => $soldier]);
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
        // สร้างข้อมูล treatment ใหม่
        $treatment = new Treatment();
        $treatment->checkin_id = $checkin->id;
        $treatment->treatment_date = now();
        $treatment->treatment_status = 'not-treated';
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
            'discharge_up' => (clone $baseQuery)->where('treatment_status', 'Discharge up')->count(),
            'follow_up' => (clone $baseQuery)->where('treatment_status', 'Follow up')->count(),
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
            'discharge_up' => (clone $filteredQuery)->where('treatment_status', 'Discharge up')->count(),
            'follow_up' => (clone $filteredQuery)->where('treatment_status', 'Follow up')->count(),
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
        $today = Carbon::today()->format('Y-m-d');
        $filterStatus = $request->input('filter', 'all'); // checkin
        $filterCaseType = $request->input('case_type', 'all');
        $filterLocation = $request->input('location', 'all');
        $filterTreatmentStatus = $request->input('treatment_status', 'all'); // treatment status filter
        $filterRiskLevel = $request->input('risk_level', 'all'); // risk level filter

        // เริ่มต้น query สำหรับการนัดหมายวันนี้
        $query = Appointment::with([
            'medicalReport.soldier',
            'medicalReport.soldier.trainingUnit',
            'medicalReport.soldier.rotation',
            'checkin',
            'checkin.treatment',  // เชื่อมกับ treatment
            'medicalReport.vitalSign'  // เชื่อมกับ vital_signs
        ])
            ->whereHas('medicalReport', function ($query) {
                // เปลี่ยนสถานะจาก approved เป็น in ER
                $query->where('status', 'in ER');
            })
            ->whereIn('status', ['scheduled', 'completed']) // รวมสถานะ scheduled และ completed
            ->whereDate('appointment_date', $today);  // กรองการนัดหมายที่มีวันที่ตรงกับวันนี้

        // 🔹 กรอง case_type
        if ($filterCaseType !== 'all') {
            $query->where('case_type', $filterCaseType);
        }

        // 🔹 กรอง location (ยกเว้น ER ถ้าเลือก all)
        if ($filterLocation !== 'all') {
            $query->where('appointment_location', $filterLocation);
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

        // 🔹 กรองสถานะ treatment_status (treated, not-treated)
        if ($filterTreatmentStatus !== 'all') {
            $query->whereHas('checkin.treatment', function ($q) use ($filterTreatmentStatus) {
                $q->where('treatment_status', $filterTreatmentStatus);
            });
        }

        // 🔹 กรองสถานะ risk_level (red, yellow, green)
        if ($filterRiskLevel !== 'all') {
            $query->whereHas('medicalReport.vitalSign', function ($q) use ($filterRiskLevel) {
                $q->where('risk_level', $filterRiskLevel);
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

            // เพิ่มข้อมูลระดับความเสี่ยง (risk_level)
            $appointment->risk_level = $appointment->medicalReport && $appointment->medicalReport->vitalSign
                ? $appointment->medicalReport->vitalSign->risk_level
                : 'not-assessed';  // ถ้าไม่มีข้อมูล risk_level จะตั้งเป็น 'not-assessed'

            return $appointment;
        });

        return view('er.scheduled_er', compact(
            'appointments',
            'filterStatus',
            'filterCaseType',
            'filterLocation',
            'filterTreatmentStatus',
            'filterRiskLevel'
        ));
    }




}