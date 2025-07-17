<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MedicalDiagnosis;
use App\Models\Treatment;
use App\Models\ICD10Disease;
class IpdController extends Controller
{
    public function admitList()
    {
        // ดึงข้อมูล appointments ที่มี treatment_status เป็น 'Admit' และ department_type เป็น 'ipd'
        $appointments = Appointment::with(['medicalReport.soldier', 'checkin.treatment.medicalDiagnosis'])
            ->whereHas('checkin.treatment.medicalDiagnosis', function ($query) {
                // เฉพาะการวินิจฉัยที่มี treatment_status = 'Admit' และ department_type = 'ipd'
                $query->where('treatment_status', 'Admit')
                    ->where('department_type', 'ipd');
            })
            ->get();

        return view('ipd.ipd_admit_list', compact('appointments'));
    }

    public function showDiagnosisForm($treatmentId)
    {

        // ดึงข้อมูลจาก Treatment พร้อม Eager Loading สำหรับ medicalDiagnosis, medicalDiagnosisDiseases และ icd10_diseases
        $treatment = Treatment::with([
            'checkin.appointment.medicalReport.soldier',
            'checkin.appointment.medicalReport.vitalSign',
            'medicalDiagnosis.diseases'// ดึงข้อมูลจาก medical_diagnosis_diseases และ icd10_diseases
        ])->findOrFail($treatmentId);


        // ข้อมูลที่จะแสดงในฟอร์ม
        $soldier = $treatment->checkin->appointment->medicalReport->soldier;
        $soldierName = $soldier->first_name . ' ' . $soldier->last_name;
        $soldierUnit = $soldier->affiliated_unit ?? 'ไม่ระบุ';
        $soldierRotation = $soldier->rotation_id ?? 'ไม่ระบุ';
        $soldierTraining = $soldier->training_unit_id ?? 'ไม่ระบุ';

        // ดึงข้อมูลจาก medical_diagnosis ที่เกี่ยวข้อง
        $medicalDiagnosis = $treatment->medicalDiagnosis;
        $doctorName = $medicalDiagnosis->doctor_name ?? '';
        // ดึงรหัสโรคและชื่อโรคจาก diseases
        $icd10Data = $treatment->medicalDiagnosis->diseases->map(function ($disease) {
            return [
                'icd10_code' => $disease->icd10_code,  // รหัสโรค
                'disease_name' => $disease->disease_name_en  // ชื่อโรค
            ];
        });
        $icd10Codes = $icd10Data->pluck('icd10_code')->toArray();

        $diseaseNames = $icd10Data->pluck('disease_name')->toArray();
        $notes = $medicalDiagnosis->notes ?? '';
        $treatmentStatus = $medicalDiagnosis->treatment_status ?? '';

        // ดึงข้อมูล vitalSign ถ้ามี
        $vitalSign = $treatment->checkin->appointment->medicalReport->vitalSign;
        $temperature = $vitalSign->temperature ?? '-';
        $bloodPressure = $vitalSign->blood_pressure ?? '-';
        $heartRate = $vitalSign->heart_rate ?? '-';

        return view('ipd.ipd_diagnosis_form', compact(
            'soldierName',
            'soldierUnit',
            'soldierRotation',
            'soldierTraining',
            'temperature',
            'bloodPressure',
            'heartRate',
            'doctorName',
            'icd10Data',
            'notes',
            'treatmentStatus',
            'treatmentId'
        ));
    }

    public function updateDiagnosisForm(Request $request, $treatmentId)
    {
        $treatment = Treatment::findOrFail($treatmentId);

        // อัปเดตข้อมูลใน MedicalDiagnosis
        $medicalDiagnosis = $treatment->medicalDiagnosis;
        $medicalDiagnosis->doctor_name = $request->input('doctor_name');
        $medicalDiagnosis->treatment_status = $request->input('treatment_status');
        $medicalDiagnosis->notes = $request->input('notes');
        $medicalDiagnosis->save();  // บันทึกการอัปเดตข้อมูลการวินิจฉัย

        // อัปเดตข้อมูล Diseases
        if ($request->has('icd10_code')) {
            $medicalDiagnosis->diseases()->detach(); // ลบความสัมพันธ์เก่า
            $codes = explode(',', $request->input('icd10_code')); // แยกรหัสโรคที่กรอกมา

            foreach ($codes as $code) {
                $disease = ICD10Disease::where('icd10_code', trim($code))->first();
                if ($disease) {
                    $medicalDiagnosis->diseases()->attach($disease->id);  // เพิ่มความสัมพันธ์ใหม่
                }
            }
        }

        // อัปเดตข้อมูล VitalSign
        if ($request->has('temperature')) {
            $vitalSign = $treatment->checkin->appointment->medicalReport->vitalSign;
            $vitalSign->temperature = $request->input('temperature');
            $vitalSign->blood_pressure = $request->input('blood_pressure');
            $vitalSign->heart_rate = $request->input('heart_rate');
            $vitalSign->save();  // บันทึกข้อมูล VitalSign ที่อัปเดต
        }

        return redirect()->route('ipd_diagnosis.page', $treatmentId)->with('success', 'ข้อมูลการวินิจฉัยถูกอัปเดต');
    }

    // ใน IpdController
    public function getDiseaseInfoByCodes($code)
    {
        // ตรวจสอบว่ามีการส่งรหัสโรคมา
        if (empty($code)) {
            return response()->json(['message' => 'กรุณาระบุรหัสโรค'], 400);
        }

        // แยกรหัสโรคจากคำขอ
        $codeArray = explode(',', $code);

        // ดึงข้อมูลโรคจากฐานข้อมูล
        $diseases = ICD10Disease::whereIn('icd10_code', $codeArray)->get();

        if ($diseases->isEmpty()) {
            return response()->json(['message' => 'ไม่พบข้อมูลโรค'], 404);
        }

        // แปลงข้อมูลให้เป็นคำอธิบายโรค
        $diseaseDescriptions = $diseases->map(function ($disease) {
            return [
                'icd10_code' => $disease->icd10_code,
                'disease_name' => $disease->disease_name_en,
            ];
        });

        // ส่งข้อมูลคำอธิบายโรคกลับไป
        return response()->json(['diseases' => $diseaseDescriptions]);
    }
    private function createFollowUpAppointment($treatmentId, $followUpDate, $appointmentLocation, $caseType)
    {
        // ตัวอย่างการสร้างการนัดหมายใหม่
        $followUpAppointment = new Appointment([
            'treatment_id' => $treatmentId,
            'appointment_date' => $followUpDate,
            'location' => $appointmentLocation,
            'case_type' => $caseType,
        ]);

        return $followUpAppointment->save();
    }





    public function dashboardIpd()
    {
        // วันที่ปัจจุบัน
        $today = Carbon::today();

        // ดึงข้อมูลผู้ป่วยที่ Admit IPD วันนี้ (ใช้ whereDate สำหรับ DATETIME)
        $admitPatientsToday = DB::table('medical_diagnosis as md')
            ->join('treatment as t', 'md.treatment_id', '=', 't.id')
            ->join('checkin as c', 't.checkin_id', '=', 'c.id')
            ->join('appointment as a', 'c.appointment_id', '=', 'a.id')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->join('soldier as s', 'mr.soldier_id', '=', 's.id')
            ->join('training_unit as tu', 's.training_unit_id', '=', 'tu.id')
            ->join('rotation as r', 's.rotation_id', '=', 'r.id')
            ->join('medical_diagnosis_diseases as mdd', 'md.id', '=', 'mdd.medical_diagnosis_id')
            ->join('icd10_diseases as icd', 'mdd.icd10_disease_id', '=', 'icd.id')
            ->select(
                's.first_name',
                's.last_name',
                's.soldier_id_card',
                'r.rotation_name',
                'tu.unit_name as training_unit_name',
                's.affiliated_unit',
                'md.treatment_status',
                DB::raw('GROUP_CONCAT(DISTINCT icd.icd10_code) as icd10_codes'),
                DB::raw('GROUP_CONCAT(DISTINCT icd.disease_name_en) as disease_names'),
                'md.diagnosis_date'
            )
            ->where('md.department_type', 'ipd')
            ->where('md.treatment_status', 'Admit')
            ->whereDate('md.diagnosis_date', $today) // ใช้ whereDate แทน where
            ->groupBy(
                's.id',
                'md.id',
                'r.rotation_name',
                'tu.unit_name',
                's.affiliated_unit',
                'md.treatment_status',
                'md.diagnosis_date'
            )
            ->orderByDesc('md.diagnosis_date')
            ->get();

        // จำนวนผู้ป่วย Admit IPD วันนี้
        $admitToday = $admitPatientsToday->count();

        // ดึงข้อมูลผู้ป่วย Admit IPD ทั้งหมด (ไม่กรองวันที่)
        $admitPatientsTotal = DB::table('medical_diagnosis as md')
            ->join('treatment as t', 'md.treatment_id', '=', 't.id')
            ->join('checkin as c', 't.checkin_id', '=', 'c.id')
            ->join('appointment as a', 'c.appointment_id', '=', 'a.id')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->join('soldier as s', 'mr.soldier_id', '=', 's.id')
            ->join('training_unit as tu', 's.training_unit_id', '=', 'tu.id')
            ->join('rotation as r', 's.rotation_id', '=', 'r.id')
            ->join('medical_diagnosis_diseases as mdd', 'md.id', '=', 'mdd.medical_diagnosis_id')
            ->join('icd10_diseases as icd', 'mdd.icd10_disease_id', '=', 'icd.id')
            ->select(
                's.first_name',
                's.last_name',
                's.soldier_id_card',
                'r.rotation_name',
                'tu.unit_name as training_unit_name',
                's.affiliated_unit',
                'md.treatment_status',
                DB::raw('GROUP_CONCAT(DISTINCT icd.icd10_code) as icd10_codes'),
                DB::raw('GROUP_CONCAT(DISTINCT icd.disease_name_en) as disease_names'),
                'md.diagnosis_date'
            )
            ->where('md.department_type', 'ipd')
            ->where('md.treatment_status', 'Admit')
            ->groupBy(
                's.id',
                'md.id',
                'r.rotation_name',
                'tu.unit_name',
                's.affiliated_unit',
                'md.treatment_status',
                'md.diagnosis_date'
            )
            ->orderByDesc('md.diagnosis_date')
            ->get();

        // จำนวนผู้ป่วย Admit IPD สะสม
        $admitTotal = $admitPatientsTotal->count();

        // จำนวนผู้ป่วย Discharge IPD วันนี้
        $dischargeToday = DB::table('medical_diagnosis as md')
            ->join('treatment as t', 'md.treatment_id', '=', 't.id')
            ->join('checkin as c', 't.checkin_id', '=', 'c.id')
            ->join('appointment as a', 'c.appointment_id', '=', 'a.id')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->where('md.department_type', 'ipd')
            ->where('md.treatment_status', 'Discharge')
            ->whereDate('md.diagnosis_date', $today)
            ->count();

        // ส่งข้อมูลไปยัง View
        return view('ipd.dashboard_ipd', compact(
            'admitToday',
            'admitTotal',
            'dischargeToday',
            'admitPatientsTotal' // หรืออาจส่ง $admitPatientsToday แยกต่างหาก
        ));
    }




    public function ipdDiagnosisStats(Request $request)
    {
        $today = Carbon::today();
        $filterStatus = $request->query('status');
        $dateFilter = $request->query('date_filter', 'today');

        // วันที่ custom
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // เงื่อนไขเฉพาะแผนก IPD
        $baseQuery = MedicalDiagnosis::with('medicalReport.soldier')
            ->where('department_type', 'ipd'); // เปลี่ยนจาก 'opd' เป็น 'ipd'

        // ✅ ยอดรวมทั้งหมด
        // แก้ไขให้ key ตรงกับที่ View ใช้งาน
        $totalStats = [
            'admit' => (clone $baseQuery)->where('treatment_status', 'Admit')->count(),
            'refer' => (clone $baseQuery)->where('treatment_status', 'Refer')->count(),
            'discharge' => (clone $baseQuery)->where('treatment_status', 'Discharge up')->count(), // เปลี่ยนจาก 'discharge_up'
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
            'discharge' => (clone $filteredQuery)->where('treatment_status', 'Discharge up')->count(), // เปลี่ยนจาก 'discharge_up'
            'follow_up' => (clone $filteredQuery)->where('treatment_status', 'Follow up')->count(),
        ];

        // ✅ ดึงข้อมูลการวินิจฉัยทั้งหมด (เฉพาะ ipd)
        $diagnosisList = MedicalDiagnosis::with([
            'medicalReport',
            'medicalReport.soldier',
            'medicalReport.soldier.trainingUnit',
            'medicalReport.soldier.rotation'
        ])
            ->where('department_type', 'ipd') // เปลี่ยนจาก 'opd' เป็น 'ipd'
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
            ->where('md.department_type', 'ipd') // เปลี่ยนจาก 'opd' เป็น 'ipd'
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

        return view('ipd.history_ipd', compact(
            'totalStats',
            'todayStats',
            'diagnosisList',
            'patientDetails'
        ));
    }




    public function getPatientDetails(Request $request)
    {
        // วันที่ปัจจุบัน
        $today = Carbon::today()->toDateString();

        // ตัวกรองจาก Request
        $filterStatus = $request->input('filter_status', 'Admit'); // สถานะการรักษา (ตั้งค่า default เป็น 'Admit')
        $filterUnit = $request->input('unit', 'all'); // หน่วย
        $filterRotation = $request->input('rotation', 'all'); // ผลัด
        $dateFilter = $request->input('date_filter', 'today'); // ตัวกรองวันที่
        $startDate = $request->input('start_date'); // วันที่เริ่มต้น
        $endDate = $request->input('end_date'); // วันที่สิ้นสุด

        // เริ่มต้น query สำหรับการดึงข้อมูลจากตาราง medical_diagnosis
        $query = DB::table('medical_diagnosis as md')
            ->join('treatment as t', 'md.treatment_id', '=', 't.id')
            ->join('checkin as c', 't.checkin_id', '=', 'c.id')
            ->join('appointment as a', 'c.appointment_id', '=', 'a.id')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->join('soldier as s', 'mr.soldier_id', '=', 's.id')
            ->join('training_unit as tu', 's.training_unit_id', '=', 'tu.id')
            ->join('rotation as r', 's.rotation_id', '=', 'r.id')
            ->leftJoin('medical_diagnosis_diseases as mdd', 'md.id', '=', 'mdd.medical_diagnosis_id')
            ->leftJoin('icd10_diseases as icd', 'mdd.icd10_disease_id', '=', 'icd.id')
            ->select(
                's.first_name',
                's.last_name',
                's.soldier_id_card',
                'r.rotation_name',
                'tu.unit_name as training_unit_name',
                's.affiliated_unit',
                'md.treatment_status',
                DB::raw('GROUP_CONCAT(DISTINCT icd.icd10_code) as icd10_codes'),
                DB::raw('GROUP_CONCAT(DISTINCT icd.disease_name_en) as disease_names'),
                'md.diagnosis_date'
            )
            ->where('md.treatment_status', 'Admit'); // กรองเฉพาะการรับเข้ารักษา (Admit)

        // ฟิลเตอร์หน่วย (unit)
        if ($filterUnit !== 'all') {
            $query->where('tu.unit_name', $filterUnit);
        }

        // ฟิลเตอร์ผลัด (rotation)
        if ($filterRotation !== 'all') {
            $query->where('r.rotation_name', $filterRotation);
        }

        // ฟิลเตอร์วันที่
        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereBetween('md.diagnosis_date', [$startDate, $endDate]);
        } elseif ($dateFilter === 'today') {
            $query->whereDate('md.diagnosis_date', $today);
        }

        // ดึงข้อมูลผู้ป่วย
        $patientDetails = $query->groupBy(
            's.first_name',
            's.last_name',
            's.soldier_id_card',
            'r.rotation_name',
            'tu.unit_name',
            's.affiliated_unit',
            'md.treatment_status',
            'md.diagnosis_date'
        )
            ->orderBy('md.diagnosis_date', 'desc')
            ->get();

        // ส่งข้อมูลไปยัง View หรือ JSON Response
        return view('ipd.view_admit', ['patientDetails' => $patientDetails]);
    }


}

