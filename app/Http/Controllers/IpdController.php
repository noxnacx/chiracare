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
        // ดึง appointments ที่มี treatment ล่าสุด (diagnosis ล่าสุดของแต่ละ treatment_id)
        $appointments = Appointment::with([
            'medicalReport.soldier',
            'checkin.treatment.medicalDiagnosis' => function ($query) {
                $query->latest('diagnosis_date'); // ดึง diagnosis ล่าสุด
            }
        ])
            ->whereHas('checkin.treatment.medicalDiagnosis', function ($query) {
                $query->whereIn('id', function ($sub) {
                    $sub->selectRaw('MAX(id)')
                        ->from('medical_diagnosis')
                        ->groupBy('treatment_id');
                })
                    ->where('treatment_status', 'Admit')
                    ->where('department_type', 'ipd');
            })
            ->get();

        return view('ipd.ipd_admit_list', compact('appointments'));
    }

    public function showDiagnosisForm($treatmentId)
    {
        $treatment = Treatment::with([
            'checkin.appointment.medicalReport.soldier',
            'checkin.appointment.medicalReport.vitalSign',
            'medicalDiagnosis.diseases'
        ])->findOrFail($treatmentId);

        // 👤 ทหาร
        $soldier = $treatment->checkin->appointment->medicalReport->soldier;
        $soldierName = $soldier->first_name . ' ' . $soldier->last_name;
        $soldierUnit = $soldier->affiliated_unit ?? 'ไม่ระบุ';
        $soldierIdCard = $soldier->soldier_id_card ?? 'ไม่ระบุ';
        $soldierRotation = $soldier->rotation->rotation_name ?? 'ไม่ระบุ';
        $soldierTraining = $soldier->trainingUnit->unit_name ?? 'ไม่ระบุ';

        // 🆕 ข้อมูลเพิ่มเติมของทหาร
        $soldierWeight = $soldier->weight_kg ?? 'ไม่ระบุ';
        $soldierHeight = $soldier->height_cm ?? 'ไม่ระบุ';
        $soldierAllergies = $soldier->medical_allergy_food_history ?? 'ไม่ระบุ';
        $soldierUnderlyingDiseases = $soldier->underlying_diseases ?? 'ไม่ระบุ';
        $soldierSelectionMethod = $soldier->selection_method ?? 'ไม่ระบุ';
        $soldierServiceDuration = $soldier->service_duration ?? 'ไม่ระบุ';

        // 📊 คำนวณ BMI (ถ้ามีข้อมูลครบ)
        $soldierBMI = null;
        if ($soldier->weight_kg && $soldier->height_cm) {
            $heightInMeters = $soldier->height_cm / 100;
            $soldierBMI = round($soldier->weight_kg / ($heightInMeters * $heightInMeters), 2);
        }

        // 🔁 วินิจฉัยย้อนหลังทั้งหมด
        $previousDiagnoses = MedicalDiagnosis::with('diseases')
            ->where('treatment_id', $treatmentId)
            ->where('department_type', 'ipd')
            ->orderByDesc('diagnosis_date')
            ->get();

        // 🆕 ใช้อันล่าสุดมาเติมค่าในฟอร์ม
        $latestDiagnosis = $previousDiagnoses->first();

        // 🔢 สัญญาณชีพ
        $vitalSigns = $treatment->checkin->appointment->medicalReport->vitalSign;

        return view('ipd.ipd_diagnosis_form', compact(
            'treatmentId',
            'soldierName',
            'soldierUnit',
            'soldierRotation',
            'soldierTraining',
            'soldierIdCard',
            'soldierWeight',
            'soldierHeight',
            'soldierAllergies',
            'soldierUnderlyingDiseases',
            'soldierSelectionMethod',
            'soldierServiceDuration',
            'latestDiagnosis',
            'vitalSigns',
            'previousDiagnoses'
        ));
    }
    //บันทึกวินิฉัย
    public function storeNewDiagnosis(Request $request, $treatmentId)
    {
        $treatment = Treatment::with('checkin.appointment.medicalReport.vitalSign')->findOrFail($treatmentId);

        // ✅ สร้างวินิจฉัยใหม่ทุกครั้ง
        $diagnosis = MedicalDiagnosis::create([
            'treatment_id' => $treatmentId,
            'doctor_name' => $request->doctor_name,
            'treatment_status' => $request->treatment_status,
            'department_type' => 'ipd',
            'vital_signs_id' => $treatment->checkin->appointment->medicalReport->vitalSign->id,
            'diagnosis_date' => now(),
            'notes' => $request->notes,
            'training_instruction' => $request->input('training_instruction'),
        ]);

        // ✅ แนบรหัสโรค ICD10 ใหม่
        if ($request->has('icd10_code')) {
            $codes = explode(',', $request->icd10_code);
            foreach ($codes as $code) {
                $disease = ICD10Disease::where('icd10_code', trim($code))->first();
                if ($disease) {
                    $diagnosis->diseases()->attach($disease->id);
                }
            }
        }

        // ✅ อัปเดต vital sign ถ้ามีการแก้ไข
        $vital = $treatment->checkin->appointment->medicalReport->vitalSign;
        if ($vital && $request->has(['temperature', 'blood_pressure', 'heart_rate'])) {
            $vital->update([
                'temperature' => $request->temperature,
                'blood_pressure' => $request->blood_pressure,
                'heart_rate' => $request->heart_rate,
                'recorded_at' => now()
            ]);
        }

        return redirect()->route('ipd.admit_list', $treatmentId)->with('success', 'บันทึกวินิจฉัยใหม่สำเร็จ');
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
        $latestAdmitPatients = DB::table('medical_diagnosis as md')
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
            ->whereIn('md.id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('medical_diagnosis')
                    ->groupBy('treatment_id');
            })
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
        $admitTotal = $latestAdmitPatients->count();

        // จำนวนผู้ป่วย Admit IPD ทั้งหมด (ไม่กรอง diagnosis ล่าสุด)
        $totalAdmitIpd = DB::table('medical_diagnosis')
            ->where('department_type', 'ipd')
            ->where('treatment_status', 'Admit')
            ->count();


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

        return view('ipd.dashboard_ipd', compact(
            'admitToday',
            'admitTotal',
            'dischargeToday',
            'totalAdmitIpd',// ✅ ส่งไปแสดงผล

            'latestAdmitPatients' // ✅ ส่งตัวนี้ไปแสดงในตาราง
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

        // รับค่าฟิลเตอร์จาก request
        $filterStatus = $request->input('filter_status', 'Admit');
        $filterUnit = $request->input('unit', 'all');
        $filterRotation = $request->input('rotation', 'all');
        $dateFilter = $request->input('date_filter', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // เริ่มต้น query
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
            // ✅ เงื่อนไขเฉพาะสถานะล่าสุดของแต่ละ treatment_id
            ->where('md.treatment_status', $filterStatus)
            ->where('md.department_type', 'ipd')
            ->whereIn('md.id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('medical_diagnosis')
                    ->groupBy('treatment_id');
            });

        // ✅ ฟิลเตอร์หน่วย
        if ($filterUnit !== 'all') {
            $query->where('tu.unit_name', $filterUnit);
        }

        // ✅ ฟิลเตอร์ผลัด
        if ($filterRotation !== 'all') {
            $query->where('r.rotation_name', $filterRotation);
        }

        // ✅ ฟิลเตอร์วันที่
        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereBetween('md.diagnosis_date', [$startDate, $endDate]);
        } elseif ($dateFilter === 'today') {
            $query->whereDate('md.diagnosis_date', $today);
        }

        // ✅ ดึงข้อมูลผู้ป่วย
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

        // ✅ ส่งไปยัง view
        return view('ipd.view_admit', ['patientDetails' => $patientDetails]);
    }



}






