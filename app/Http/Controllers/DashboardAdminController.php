<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Models\MedicalDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardAdminController extends Controller
{

    public function showDashboardAdmin()
    {
        // 🗓 ดึงข้อมูลนัดหมายวันนี้ทั้งหมด (ยกเว้น ER)
        $appointments = Appointment::whereDate('appointment_date', Carbon::today())
            ->with(['medicalReport.soldier', 'checkin'])
            ->whereHas('medicalReport', function ($query) {
                $query->where('status', '!=', 'in ER');
            })
            ->get();

        // 🚨 ดึงเฉพาะนัดหมาย critical วันนี้
        $criticalAppointments = $appointments->where('case_type', 'critical');

        // 📊 นับจำนวน opd / er / ipd จาก medical_diagnosis ของวันนี้
        $departmentCounts = DB::table('medical_diagnosis')
            ->select('department_type', DB::raw('count(*) as total'))
            ->whereDate('diagnosis_date', Carbon::today())
            ->groupBy('department_type')
            ->pluck('total', 'department_type');

        $opdCount = $departmentCounts['opd'] ?? 0;
        $erCount = $departmentCounts['er'] ?? 0;
        $ipdCount = $departmentCounts['ipd'] ?? 0;

        $admitPatients = DB::table('medical_diagnosis as md')
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
            ->whereIn('md.id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('medical_diagnosis')
                    ->groupBy('treatment_id');
            })

            ->where('md.treatment_status', 'Admit')
            ->where('md.department_type', 'ipd')
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

        $ipdAdmitCount = $admitPatients->count();

        $sentCount = DB::table('medical_report')
            ->where('status', 'sent')
            ->count();

        $missedCount = DB::table('appointment')
            ->where('status', 'missed')
            ->count();


        return view('admin-hospital.dashboardadmin', [
            'appointments' => $appointments,
            'criticalAppointments' => $criticalAppointments,
            'opdCount' => $opdCount,
            'erCount' => $erCount,
            'ipdCount' => $ipdCount,
            'admitPatients' => $admitPatients,
            'ipdAdmitCount' => $ipdAdmitCount,
            'missedCount' => $missedCount,// ✅ เพิ่มตรงนี้
            // ✅ เพิ่มตัวแปรนี้
            'sentCount' => $sentCount // ✅ ตัวแปรใหม่


        ]);
    }






    // เตรียมข้อมูลสำหรับส่งไปยัง frontend
    public function alltop5Diseases(Request $request)
    {
        // ดึงข้อมูลการวินิจฉัยพร้อมชื่อโรคและรหัส
        $topDiseases = DB::table('medical_diagnosis')
            ->join('medical_diagnosis_diseases', 'medical_diagnosis.id', '=', 'medical_diagnosis_diseases.medical_diagnosis_id')
            ->join('icd10_diseases', 'medical_diagnosis_diseases.icd10_disease_id', '=', 'icd10_diseases.id')
            ->select('icd10_diseases.disease_name_en', 'icd10_diseases.icd10_code', DB::raw('count(*) as count'))
            ->groupBy('icd10_diseases.disease_name_en', 'icd10_diseases.icd10_code')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // เตรียมข้อมูลสำหรับส่งไปยัง frontend
        $topDiseasesData = $topDiseases->mapWithKeys(function ($item) {
            // จัดรูปแบบข้อมูลที่ต้องการส่งให้ frontend
            return [
                $item->icd10_code => [
                    'name' => $item->disease_name_en,
                    'count' => $item->count
                ]
            ];
        });

        // ส่งข้อมูลเป็น JSON ไปยัง frontend
        return response()->json($topDiseasesData);
    }
    public function getDiseasesData(Request $request)
    {
        try {
            $codes = array_filter(array_map('trim', explode(',', $request->input('codes'))));
            $start = $request->input('start');
            $end = $request->input('end');

            if (empty($codes)) {
                return response()->json(['error' => 'No valid codes provided'], 400);
            }

            // ดึงข้อมูลโรคทั้งหมดที่มี code ตรงกับที่ระบุ
            $allDiseases = DB::table('icd10_diseases')
                ->whereIn('icd10_code', $codes)
                ->select('disease_name_en as name', 'icd10_code as disease_code')
                ->get()
                ->keyBy('disease_code');

            // ✅ ดึงข้อมูลการวินิจฉัยล่าสุดของแต่ละ treatment_id
            $subquery = DB::table('medical_diagnosis')
                ->select('treatment_id', DB::raw('MAX(diagnosis_date) as latest_diagnosis_date'))
                ->groupBy('treatment_id');

            // ดึงข้อมูลการวินิจฉัยที่มีจริง (เฉพาะล่าสุดของแต่ละ treatment)
            $query = DB::table('medical_diagnosis')
                ->joinSub($subquery, 'latest', function ($join) {
                    $join->on('medical_diagnosis.treatment_id', '=', 'latest.treatment_id')
                        ->on('medical_diagnosis.diagnosis_date', '=', 'latest.latest_diagnosis_date');
                })
                ->join('medical_diagnosis_diseases', 'medical_diagnosis.id', '=', 'medical_diagnosis_diseases.medical_diagnosis_id')
                ->join('icd10_diseases', 'medical_diagnosis_diseases.icd10_disease_id', '=', 'icd10_diseases.id')
                ->whereIn('icd10_diseases.icd10_code', $codes);

            if ($start && $end) {
                $query->whereBetween(DB::raw('DATE(medical_diagnosis.diagnosis_date)'), [$start, $end]);
            }

            $actualData = $query
                ->select(
                    'icd10_diseases.disease_name_en as name',
                    'icd10_diseases.icd10_code as disease_code',
                    DB::raw('count(DISTINCT medical_diagnosis.treatment_id) as count')
                )
                ->groupBy('icd10_diseases.disease_name_en', 'icd10_diseases.icd10_code')
                ->get()
                ->keyBy('disease_code');

            // รวมข้อมูลทั้งหมด
            $result = [];
            foreach ($allDiseases as $code => $disease) {
                $result[] = [
                    'name' => $disease->name,
                    'disease_code' => $code,
                    'count' => isset($actualData[$code]) ? (int) $actualData[$code]->count : 0
                ];
            }

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Error fetching diseases data: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch diseases data'], 500);
        }
    }




    public function getPatientAdmit(Request $request)
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

        // Subqueries เดิม...
        $admitDateSubquery = DB::table('medical_diagnosis')
            ->select('treatment_id', DB::raw('MIN(diagnosis_date) as admit_date'))
            ->where('treatment_status', 'Admit')
            ->where('department_type', 'ipd')
            ->groupBy('treatment_id');

        $dischargeSubquery = DB::table('medical_diagnosis')
            ->select(
                'treatment_id',
                'diagnosis_date as discharge_date',
                'treatment_status as discharge_status'
            )
            ->where('department_type', 'ipd')
            ->whereIn('treatment_status', ['Discharge', 'Refer', 'Follow-up'])
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('medical_diagnosis')
                    ->where('department_type', 'ipd')
                    ->whereIn('treatment_status', ['Discharge', 'Refer', 'Follow-up'])
                    ->groupBy('treatment_id');
            });

        // เริ่มต้น query หลัก
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
            ->joinSub($admitDateSubquery, 'admit_info', 'md.treatment_id', '=', 'admit_info.treatment_id')
            ->leftJoinSub($dischargeSubquery, 'discharge_info', 'md.treatment_id', '=', 'discharge_info.treatment_id')
            ->select(
                's.first_name',
                't.id as treatment_id',
                's.last_name',
                's.soldier_id_card',
                's.soldier_image',
                'r.rotation_name',
                'tu.unit_name as training_unit_name',
                's.affiliated_unit',
                'md.treatment_status as current_status',
                'admit_info.admit_date',
                'discharge_info.discharge_date',
                'discharge_info.discharge_status',
                DB::raw('CASE
                WHEN discharge_info.discharge_date IS NOT NULL
                THEN DATEDIFF(discharge_info.discharge_date, admit_info.admit_date) + 1
                ELSE DATEDIFF(CURDATE(), admit_info.admit_date) + 1
            END as treatment_days'),
                DB::raw('CASE
                WHEN discharge_info.discharge_date IS NOT NULL
                THEN CONCAT("ออกแล้ว (", discharge_info.discharge_status, ")")
                ELSE "กำลังรักษา"
            END as admit_status'),
                DB::raw('GROUP_CONCAT(DISTINCT icd.icd10_code) as icd10_codes'),
                DB::raw('GROUP_CONCAT(DISTINCT icd.disease_name_en) as disease_names')
            )
            ->where('md.department_type', 'ipd')
            ->whereIn('md.id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('medical_diagnosis')
                    ->groupBy('treatment_id');
            });

        // ฟิลเตอร์เดิม
        if ($filterStatus !== 'all') {
            if ($filterStatus === 'Admit') {
                $query->whereNull('discharge_info.discharge_date');
            } elseif ($filterStatus === 'Discharged') {
                $query->whereNotNull('discharge_info.discharge_date');
            }
        }

        if ($filterUnit !== 'all') {
            $query->where('tu.unit_name', $filterUnit);
        }

        if ($filterRotation !== 'all') {
            $query->where('r.rotation_name', $filterRotation);
        }

        if ($dateFilter === 'custom' && $startDate && $endDate) {
            $query->whereBetween(DB::raw('DATE(admit_info.admit_date)'), [$startDate, $endDate]);
        } elseif ($dateFilter === 'today') {
            $query->whereDate('admit_info.admit_date', $today);
        } elseif ($dateFilter === 'all') {
            // ไม่กรองวันที่
        }

        // ดึงข้อมูลผู้ป่วย
        $patientDetails = $query->groupBy(
            's.first_name',
            't.id',
            's.last_name',
            's.soldier_id_card',
            's.soldier_image',
            'r.rotation_name',
            'tu.unit_name',
            's.affiliated_unit',
            'md.treatment_status',
            'admit_info.admit_date',
            'discharge_info.discharge_date',
            'discharge_info.discharge_status'
        )
            ->orderBy('admit_info.admit_date', 'desc')
            ->get();

        $patientDetails = $patientDetails->map(function ($patient) {
            if ($patient->soldier_image) {
                $patient->soldier_image_url = url("/uploads/soldiers/{$patient->soldier_image}");
            } else {
                $patient->soldier_image_url = url('/uploads/soldiers/default-avatar.png');
            }
            return $patient;
        });

        // ดึงข้อมูลสำหรับ dropdown
        $units = DB::table('training_unit')->pluck('unit_name', 'unit_name');
        $rotations = DB::table('rotation')->pluck('rotation_name', 'rotation_name');

        if ($request->wantsJson() || $request->input('format') === 'json') {
            return response()->json([
                'success' => true,
                'data' => $patientDetails,
                'meta' => [
                    'total' => $patientDetails->count(),
                    'filters' => [
                        'filter_status' => $filterStatus,
                        'unit' => $filterUnit,
                        'rotation' => $filterRotation,
                        'date_filter' => $dateFilter,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]
                ]
            ]);
        }

        return view('admin-hospital.view_admit', [
            'patientDetails' => $patientDetails,
            'units' => $units,
            'rotations' => $rotations,
            'currentFilters' => [
                'filter_status' => $filterStatus,
                'unit' => $filterUnit,
                'rotation' => $filterRotation,
                'date_filter' => $dateFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]
        ]);
    }

    public function searchPatient(Request $request)
    {
        // เริ่มบันทึก Log
        \Log::info('Starting patient search', ['query' => $request->input('query')]);

        try {
            $query = $request->input('query');

            if (empty($query)) {
                \Log::warning('Empty search query received');
                return response()->json(['status' => 'no_query']);
            }

            \Log::debug('Building database query for patient search');

            // ขั้นที่ 1: ค้นหาข้อมูลผู้ป่วยพื้นฐาน
            $patient = DB::table('soldier as s')
                ->leftJoin('training_unit as tu', 's.training_unit_id', '=', 'tu.id')
                ->leftJoin('rotation as r', 's.rotation_id', '=', 'r.id')
                ->select(
                    's.id as soldier_id',
                    's.first_name',
                    's.last_name',
                    's.soldier_id_card',
                    's.soldier_image',
                    's.weight_kg',
                    's.height_cm',
                    's.affiliated_unit',
                    's.service_duration',
                    's.selection_method',
                    's.underlying_diseases',
                    's.medical_allergy_food_history',
                    'tu.unit_name as training_unit_name',
                    'r.rotation_name as rotation_name'
                )
                ->where(function ($q) use ($query) {
                    $q->where('s.first_name', 'LIKE', "%{$query}%")
                        ->orWhere('s.last_name', 'LIKE', "%{$query}%")
                        ->orWhere('s.soldier_id_card', 'LIKE', "%{$query}%")
                        ->orWhere(DB::raw("CONCAT(s.first_name, ' ', s.last_name)"), 'LIKE', "%{$query}%");
                })
                ->first();

            \Log::debug('Basic patient search executed', ['patient_found' => $patient ? true : false]);

            if ($patient) {
                // ขั้นที่ 2: ค้นหาประวัติการรักษาพร้อม vital signs (ถ้ามี)
                $medicalHistory = DB::table('medical_diagnosis as md')
                    ->join('treatment as t', 'md.treatment_id', '=', 't.id')
                    ->join('checkin as c', 't.checkin_id', '=', 'c.id')
                    ->join('appointment as a', 'c.appointment_id', '=', 'a.id')
                    ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
                    ->leftJoin('vital_signs as vs', 'md.vital_signs_id', '=', 'vs.id')
                    ->leftJoin('medical_diagnosis_diseases as mdd', 'md.id', '=', 'mdd.medical_diagnosis_id')
                    ->leftJoin('icd10_diseases as icd', 'mdd.icd10_disease_id', '=', 'icd.id')
                    ->select(
                        'mr.symptom_description',
                        'md.doctor_name',
                        'md.training_instruction',
                        'md.department_type',
                        'md.treatment_status',
                        'md.diagnosis_date',
                        // เพิ่ม vital signs
                        'vs.temperature',
                        'vs.blood_pressure',
                        'vs.heart_rate',
                        'vs.risk_level',
                        'vs.source as vital_source',
                        'vs.recorded_at',
                        DB::raw('GROUP_CONCAT(DISTINCT icd.icd10_code) as icd10_codes'),
                        DB::raw('GROUP_CONCAT(DISTINCT icd.disease_name_en) as disease_names')
                    )
                    ->where('mr.soldier_id', '=', $patient->soldier_id)
                    ->groupBy(
                        'md.id',
                        'mr.symptom_description',
                        'md.doctor_name',
                        'md.training_instruction',
                        'md.department_type',
                        'md.treatment_status',
                        'md.diagnosis_date',
                        'vs.temperature',
                        'vs.blood_pressure',
                        'vs.heart_rate',
                        'vs.risk_level',
                        'vs.source',
                        'vs.recorded_at'
                    )
                    ->orderBy('md.diagnosis_date', 'desc')
                    ->get();

                \Log::debug('Medical history with vital signs search executed', ['history_count' => $medicalHistory->count()]);

                // รวมข้อมูลประวัติล่าสุดเข้าไปในข้อมูลผู้ป่วย
                if ($medicalHistory->isNotEmpty()) {
                    $latestRecord = $medicalHistory->first();
                    $patient->symptom_description = $latestRecord->symptom_description;
                    $patient->doctor_name = $latestRecord->doctor_name;
                    $patient->training_instruction = $latestRecord->training_instruction;
                    $patient->treatment_status = $latestRecord->treatment_status;
                    $patient->diagnosis_date = $latestRecord->diagnosis_date;
                    $patient->department_type = $latestRecord->department_type;
                    $patient->icd10_codes = $latestRecord->icd10_codes;
                    $patient->disease_names = $latestRecord->disease_names;

                    // เพิ่ม vital signs ข้อมูลล่าสุด
                    $patient->temperature = $latestRecord->temperature;
                    $patient->blood_pressure = $latestRecord->blood_pressure;
                    $patient->heart_rate = $latestRecord->heart_rate;
                    $patient->risk_level = $latestRecord->risk_level;
                    $patient->vital_source = $latestRecord->vital_source;
                    $patient->vital_recorded_at = $latestRecord->recorded_at;
                } else {
                    // ถ้าไม่มีประวัติการรักษา ให้ใส่ค่าเริ่มต้น
                    $patient->symptom_description = null;
                    $patient->doctor_name = null;
                    $patient->training_instruction = null;
                    $patient->department_type = null;
                    $patient->treatment_status = null;
                    $patient->diagnosis_date = null;
                    $patient->icd10_codes = null;
                    $patient->disease_names = null;

                    // เพิ่ม vital signs null values
                    $patient->temperature = null;
                    $patient->blood_pressure = null;
                    $patient->heart_rate = null;
                    $patient->risk_level = null;
                    $patient->vital_source = null;
                    $patient->vital_recorded_at = null;
                }

                \Log::info('Patient found with vital signs', ['patient_id' => $patient->soldier_id_card]);

                return response()->json([
                    'status' => 'found',
                    'patient' => $patient,
                    'medical_history' => $medicalHistory
                ]);
            } else {
                \Log::info('No patient found for query', ['query' => $query]);
                return response()->json(['status' => 'not_found']);
            }

        } catch (\Exception $e) {
            // บันทึกข้อผิดพลาด
            \Log::error('Patient search failed', [
                'error' => $e->getMessage(),
                'query' => $request->input('query'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการค้นหา'
            ], 500);
        }
    }
    public function searchAppointments(Request $request)
    {
        try {
            $query = $request->input('query');

            // ตรวจสอบว่ามีการส่ง query มาหรือไม่
            if (empty($query)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'กรุณาระบุชื่อทหารหรือเลขบัตรประชาชน'
                ], 400);
            }

            // ค้นหาข้อมูลผู้ป่วยและนัดหมาย
            $appointments = $this->getPatientAppointments($query);

            if ($appointments->isEmpty()) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'ไม่พบข้อมูลการนัดหมายสำหรับผู้ป่วยรายนี้'
                ]);
            }

            // จัดกลุ่มข้อมูลตามผู้ป่วย
            $patientData = $this->formatPatientAppointmentData($appointments);

            return response()->json([
                'status' => 'found',
                'data' => $patientData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการค้นหาข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPatientAppointments($query)
    {
        // ค้นหาข้อมูลทหารก่อน
        $soldiers = DB::table('soldier as s')
            ->leftJoin('rotation as r', 's.rotation_id', '=', 'r.id')
            ->leftJoin('training_unit as tu', 's.training_unit_id', '=', 'tu.id')
            ->select([
                // ข้อมูลทหารพื้นฐาน
                's.id as soldier_id',
                's.first_name',
                's.last_name',
                's.soldier_id_card',
                's.affiliated_unit',

                // ข้อมูลใหม่ที่เพิ่มเข้ามา
                's.medical_allergy_food_history',
                's.underlying_diseases',
                's.selection_method',
                's.service_duration',
                's.soldier_image',

                // ข้อมูลร่างกาย
                's.weight_kg',
                's.height_cm',

                // ข้อมูลจากตาราง rotation และ training_unit
                'r.rotation_name',
                'tu.unit_name as training_unit_name'
            ])
            ->where(function ($q) use ($query) {
                $q->whereRaw("CONCAT(s.first_name, ' ', s.last_name) LIKE ?", ["%{$query}%"])
                    ->orWhere('s.soldier_id_card', 'LIKE', "%{$query}%");
            })
            ->get();

        if ($soldiers->isEmpty()) {
            return collect(); // คืนค่า empty collection
        }

        // ดึงข้อมูลการนัดหมายของทหารที่พบ
        $soldierIds = $soldiers->pluck('soldier_id')->toArray();

        $appointments = DB::table('appointment as a')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->select([
                'mr.soldier_id',
                'a.id as appointment_id',
                'a.appointment_date',
                'a.appointment_location',
                'a.case_type',
                'a.status',
                'a.is_follow_up',
                'mr.id as medical_report_id',
                'mr.symptom_description',
                'mr.report_date'
            ])
            ->whereIn('mr.soldier_id', $soldierIds)
            ->whereIn('a.status', ['scheduled', 'missed'])
            ->orderBy('a.appointment_date', 'desc') // เรียงวันที่ล่าสุดก่อน
            ->get();

        // รวมข้อมูลทหารกับการนัดหมาย
        $result = [];
        foreach ($soldiers as $soldier) {
            $soldierAppointments = $appointments->where('soldier_id', $soldier->soldier_id);

            if ($soldierAppointments->isNotEmpty()) {
                // มีการนัดหมาย - เพิ่มข้อมูลการนัดหมายเข้าไป
                foreach ($soldierAppointments as $appointment) {
                    $combinedData = (object) array_merge((array) $soldier, (array) $appointment);
                    $result[] = $combinedData;
                }
            } else {
                // ไม่มีการนัดหมาย - เพิ่มเฉพาะข้อมูลทหาร
                $soldierOnly = (object) array_merge((array) $soldier, [
                    'appointment_id' => null,
                    'appointment_date' => null,
                    'appointment_location' => null,
                    'case_type' => null,
                    'status' => null,
                    'is_follow_up' => null,
                    'medical_report_id' => null,
                    'symptom_description' => null,
                    'report_date' => null
                ]);
                $result[] = $soldierOnly;
            }
        }

        return collect($result);
    }

    /**
     * จัดรูปแบบข้อมูลผู้ป่วยและนัดหมาย
     */
    private function formatPatientAppointmentData($appointments)
    {
        $patientData = [];

        foreach ($appointments as $appointment) {
            $soldierId = $appointment->soldier_id;

            // สร้างข้อมูลทหารถ้ายังไม่มี
            if (!isset($patientData[$soldierId])) {
                $patientData[$soldierId] = [
                    'soldier_info' => [
                        'id' => $appointment->soldier_id,
                        'name' => trim($appointment->first_name . ' ' . $appointment->last_name),
                        'soldier_id_card' => $appointment->soldier_id_card,
                        'rotation_name' => $appointment->rotation_name ?? 'ไม่ระบุผลัด',
                        'training_unit_name' => $appointment->training_unit_name ?? 'ไม่ระบุหน่วยฝึก',
                        'affiliated_unit' => $appointment->affiliated_unit ?? 'ไม่ระบุหน่วยต้นสังกัด',

                        // ข้อมูลทางการแพทย์ใหม่
                        'medical_info' => [
                            'allergy_food_history' => $appointment->medical_allergy_food_history ?? 'ไม่มีประวัติแพ้',
                            'underlying_diseases' => $appointment->underlying_diseases ?? 'ไม่มีโรคประจำตัว'
                        ],

                        // ข้อมูลร่างกาย
                        'physical_info' => [
                            'weight_kg' => $appointment->weight_kg,
                            'height_cm' => $appointment->height_cm
                        ],

                        // ข้อมูลการรับราชการ
                        'service_info' => [
                            'selection_method' => $appointment->selection_method,
                            'service_duration' => $appointment->service_duration
                        ],

                        // รูปภาพทหาร
                        'soldier_image' => $this->formatSoldierImage($appointment->soldier_image)
                    ],
                    'appointments' => []
                ];
            }

            // เพิ่มข้อมูลการนัดหมาย - ตรวจสอบว่ามีการนัดหมายจริงหรือไม่
            if ($appointment->appointment_id !== null) {
                $appointmentData = $this->formatSingleAppointment($appointment);
                $patientData[$soldierId]['appointments'][] = $appointmentData;
            }
            // หากไม่มีการนัดหมาย จะไม่เพิ่มข้อมูลการนัดหมายใดๆ (appointments array จะเป็น empty)
        }

        // เรียงลำดับ appointments ในแต่ละ patient ให้วันที่เก่าที่สุดอยู่ก่อน
        foreach ($patientData as $soldierId => &$patient) {
            if (!empty($patient['appointments']) && count($patient['appointments']) > 1) {
                // เรียงลำดับ appointments ตาม appointment_date (วันที่เก่าก่อน)
                $appointments = $patient['appointments'];

                // ใช้ array_multisort เพื่อเรียงลำดับ
                $dates = array_column($appointments, 'appointment_date');
                array_multisort($dates, SORT_ASC, $appointments); // SORT_ASC = วันที่เก่าก่อน

                $patient['appointments'] = $appointments;
            }
        }
        unset($patient); // ทำลาย reference

        return array_values($patientData);
    }

    /**
     * จัดรูปแบบข้อมูลการนัดหมายแต่ละรายการ
     * method นี้จะถูกเรียกเฉพาะเมื่อ appointment_id ไม่เป็น null
     *
     * @param object $appointment
     * @return array
     */
    private function formatSingleAppointment($appointment)
    {
        // ตรวจสอบอีกครั้งเพื่อความปลอดภัย
        if ($appointment->appointment_date === null) {
            return null;
        }

        $appointmentDate = Carbon::parse($appointment->appointment_date);

        return [
            'id' => $appointment->appointment_id,
            'appointment_date' => $appointmentDate->format('Y-m-d H:i:s'),
            'appointment_date_thai' => $appointmentDate->locale('th')->translatedFormat('j F Y เวลา H:i น.'),
            'appointment_location' => $appointment->appointment_location,
            'appointment_location_thai' => $this->translateLocation($appointment->appointment_location),
            'case_type' => $appointment->case_type,
            'case_type_thai' => $this->translateCaseType($appointment->case_type),
            'status' => $appointment->status,
            'status_thai' => $this->translateStatus($appointment->status),
            'is_follow_up' => (bool) $appointment->is_follow_up,
            'follow_up_text' => $appointment->is_follow_up ? 'นัดติดตาม' : 'นัดปกติ',

            // ข้อมูล Medical Report
            'medical_report' => [
                'id' => $appointment->medical_report_id,
                'symptom_description' => $appointment->symptom_description,
                'report_date' => $appointment->report_date
            ]
        ];
    }

    /**
     * แปลสถานที่นัดหมายเป็นภาษาไทย
     *
     * @param string $location
     * @return string
     */
    private function translateLocation($location)
    {
        $translations = [
            'OPD' => 'OPD',
            'ER' => 'ER',
            'IPD' => 'IPD',
            'ARI clinic' => 'ARI clinic',
            'กองทันตกรรม' => 'กองทันตกรรม'
        ];

        return $translations[$location] ?? $location;
    }

    /**
     * แปลประเภทเคสเป็นภาษาไทย
     *
     * @param string $caseType
     * @return string
     */
    private function translateCaseType($caseType)
    {
        $translations = [
            'normal' => 'เคสปกติ',
            'critical' => 'เคสวิกฤต'
        ];

        return $translations[$caseType] ?? $caseType;
    }

    /**
     * แปลสถานะเป็นภาษาไทย
     *
     * @param string $status
     * @return string
     */
    private function translateStatus($status)
    {
        $translations = [
            'scheduled' => 'นัดแล้ว',
            'missed' => 'ไม่มาตามนัด'
        ];

        return $translations[$status] ?? $status;
    }

    /**
     * จัดรูปแบบรูปภาพทหาร
     */
    private function formatSoldierImage($imageFilename)
    {
        if (empty($imageFilename)) {
            return null;
        }

        // ตรวจสอบว่ามี path อยู่แล้วหรือไม่
        if (strpos($imageFilename, 'uploads/soldiers/') === 0) {
            // หากมี path อยู่แล้ว ให้ return ตรงๆ
            return $imageFilename;
        }

        // หากเป็นแค่ filename ให้เพิ่ม path
        return 'uploads/soldiers/' . $imageFilename;
    }

    /**
     * ดึงรูปภาพทหาร (เรียกแยกเพื่อประสิทธิภาพ)
     */
    private function getSoldierImage($soldierId)
    {
        $soldier = DB::table('soldier')
            ->select('soldier_image')
            ->where('id', $soldierId)
            ->first();

        if ($soldier && $soldier->soldier_image) {
            return $this->formatSoldierImage($soldier->soldier_image);
        }

        return null;
    }
    // ✅ แก้ไขใน Backend function getMedicalReportsWithSoldierInfo()

    public function getMedicalReportsWithSoldierInfo(Request $request, $statuses = null)
    {
        // ถ้าไม่ส่ง statuses มา ให้ดูจาก request parameter
        if ($statuses === null) {
            $statusParam = $request->get('status');

            if ($statusParam === 'sent') {
                $statuses = ['sent'];
            } elseif ($statusParam === 'approved') {
                $statuses = ['approved'];
            } else {
                $statuses = ['sent', 'approved']; // default
            }
        }

        // ✅ เพิ่มตัวแปรสำหรับการกรอง
        $date = $request->get('date');
        $rotationId = $request->get('rotation_id');
        $trainingUnitId = $request->get('training_unit_id');
        $caseType = $request->get('case_type');
        $today = now()->toDateString();

        // ✅ แก้ไข: ถ้าไม่มีการกรองวันที่ ให้ใช้วันนี้สำหรับ approved เท่านั้น
        // ถ้ามีการกรองวันที่ ให้ใช้วันที่ที่เลือก
        $appointmentDate = $date ?: ($request->has('date') ? null : $today);

        // Query หลัก - เอาแค่ชุดเดียว
        $baseQuery = DB::table('medical_report as mr')
            ->join('soldier as s', 'mr.soldier_id', '=', 's.id')
            ->join('rotation as r', 's.rotation_id', '=', 'r.id')
            ->join('training_unit as tu', 's.training_unit_id', '=', 'tu.id')
            ->leftJoin('vital_signs as vs', 'mr.vital_signs_id', '=', 'vs.id')
            ->leftJoin('appointment as a', function ($join) use ($appointmentDate, $statuses) {
                $join->on('mr.id', '=', 'a.medical_report_id')
                    ->where('a.status', '=', 'scheduled');

                // ✅ แก้ไข: กรองวันที่ appointment เฉพาะเมื่อต้องการ approved
                if (in_array('approved', $statuses) && $appointmentDate) {
                    $join->whereDate('a.created_at', '=', $appointmentDate);
                } elseif (in_array('approved', $statuses) && !$appointmentDate) {
                    // ถ้าไม่ระบุวันที่ แสดง approved ทั้งหมด
                    // ไม่กรองวันที่
                }
            });

        // ✅ กรองตามสถานะ - แยกเงื่อนไขให้ชัดเจน
        $baseQuery->where(function ($query) use ($statuses, $appointmentDate) {
            if (in_array('sent', $statuses) && !in_array('approved', $statuses)) {
                // แค่ sent เท่านั้น
                $query->where('mr.status', '=', 'sent');
            } elseif (in_array('approved', $statuses) && !in_array('sent', $statuses)) {
                // แค่ approved ที่มี appointment เท่านั้น
                if ($appointmentDate) {
                    $query->where('mr.status', '=', 'approved')
                        ->whereNotNull('a.id');
                } else {
                    // ถ้าไม่ระบุวันที่ แสดง approved ทั้งหมด
                    $query->where('mr.status', '=', 'approved');
                }
            } else {
                // ทั้ง sent และ approved
                $query->where('mr.status', '=', 'sent');

                if ($appointmentDate) {
                    $query->orWhere(function ($subQuery) {
                        $subQuery->where('mr.status', '=', 'approved')
                            ->whereNotNull('a.id');
                    });
                } else {
                    $query->orWhere('mr.status', '=', 'approved');
                }
            }
        });

        // ✅ กรองตามผลัด
        if ($rotationId) {
            $baseQuery->where('s.rotation_id', '=', $rotationId);
        }

        // ✅ กรองตามหน่วยฝึก
        if ($trainingUnitId) {
            $baseQuery->where('s.training_unit_id', '=', $trainingUnitId);
        }

        // ✅ กรองตามประเภทผู้ป่วย
        if ($caseType && $caseType !== 'all') {
            $baseQuery->where('a.case_type', '=', $caseType);
        }

        // ✅ กรองตามวันที่รายงานทางการแพทย์ (สำหรับ sent)
        if ($date && in_array('sent', $statuses)) {
            $baseQuery->whereDate('mr.report_date', '=', $date);
        }

        // ดึงข้อมูลรายละเอียด
        $reports = $baseQuery->select([
            'mr.id as medical_report_id',
            'mr.status as medical_report_status',
            'mr.symptom_description',
            'mr.pain_score',
            'mr.report_date',
            's.id as soldier_id',
            's.soldier_id_card',
            's.first_name',
            's.last_name',
            's.affiliated_unit',
            'r.id as rotation_id',
            'r.rotation_name',
            'tu.id as training_unit_id',
            'tu.unit_name',
            'a.id as appointment_id',
            'a.appointment_date',
            'a.appointment_location',
            'a.case_type',
            'a.status as appointment_status',
            'a.created_at as appointment_created_at',
            'vs.risk_level'
        ])
            ->orderBy('vs.risk_level', 'desc')
            ->orderBy('mr.status', 'desc')
            ->orderBy('mr.report_date', 'desc')
            ->get();

        // ✅ นับจำนวนตามสถานะและการกรอง
        $sentCount = $reports->where('medical_report_status', 'sent')->count();
        $approvedCount = $reports->where('medical_report_status', 'approved')->count();

        // ✅ นับตามประเภทเคส
        $normalCaseCount = $reports->where('case_type', 'normal')->count();
        $criticalCaseCount = $reports->where('case_type', 'critical')->count();

        // ✅ นับตามระดับเสี่ยง
        $redRiskCount = $reports->where('risk_level', 'red')->count();
        $yellowRiskCount = $reports->where('risk_level', 'yellow')->count();
        $greenRiskCount = $reports->where('risk_level', 'green')->count();

        // ✅ เพิ่ม Debug Log
        \Log::info('getMedicalReportsWithSoldierInfo Debug', [
            'request_params' => $request->all(),
            'statuses' => $statuses,
            'filters' => [
                'date' => $date,
                'appointment_date' => $appointmentDate,
                'rotation_id' => $rotationId,
                'training_unit_id' => $trainingUnitId,
                'case_type' => $caseType
            ],
            'results_count' => $reports->count(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $reports,
            'summary' => [
                'total' => $reports->count(),
                'sent_count' => $sentCount,
                'approved_with_appointment_today' => $approvedCount,
                'case_counts' => [
                    'normal' => $normalCaseCount,
                    'critical' => $criticalCaseCount
                ],
                'risk_counts' => [
                    'red' => $redRiskCount,
                    'yellow' => $yellowRiskCount,
                    'green' => $greenRiskCount
                ]
            ],
            'filters_applied' => [
                'date' => $date,
                'appointment_date' => $appointmentDate,
                'rotation_id' => $rotationId,
                'training_unit_id' => $trainingUnitId,
                'case_type' => $caseType,
                'statuses' => $statuses
            ]
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    public function getCurrentMonthTopDiseases()
    {
        // โปรแกรมจับเดือนปัจจุบันอัตโนมัติ
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // ดึงข้อมูลการวินิจฉัยล่าสุดของแต่ละ Episode (ตาม treatment_id และ doctor_name)
        $latestDiagnosesSubquery = DB::table('medical_diagnosis as md1')
            ->select('md1.*')
            ->whereRaw('md1.id = (
                SELECT md2.id
                FROM medical_diagnosis md2
                WHERE md2.treatment_id = md1.treatment_id
                AND md2.doctor_name = md1.doctor_name
                ORDER BY
                    CASE
                        WHEN md2.treatment_status IN ("Refer", "Discharge", "Follow-up") THEN 1
                        WHEN md2.treatment_status = "Admit" THEN 2
                        ELSE 3
                    END,
                    md2.diagnosis_date DESC,
                    md2.id DESC
                LIMIT 1
            )')
            ->whereBetween('diagnosis_date', [$startOfMonth, $endOfMonth]);

        // นับจำนวนการวินิจฉัยทั้งหมด (จากข้อมูลล่าสุดเท่านั้น)
        $totalDiagnoses = DB::table(DB::raw("({$latestDiagnosesSubquery->toSql()}) as latest_md"))
            ->mergeBindings($latestDiagnosesSubquery)
            ->count();

        if ($totalDiagnoses == 0) {
            return response()->json([
                'message' => 'ไม่มีข้อมูลการวินิจฉัยในเดือน ' . $this->formatThaiMonth($currentMonth),
                'data' => [],
                'auto_detected_period' => [
                    'thai_month' => $this->formatThaiMonth($currentMonth),
                    'month' => $currentMonth->month,
                    'year' => $currentMonth->year,
                    'thai_year' => $currentMonth->year + 543
                ]
            ]);
        }

        // ดึง 4 โรคที่พบเยอะที่สุด (จากข้อมูลล่าสุดเท่านั้น)
        $topDiseases = DB::table(DB::raw("({$latestDiagnosesSubquery->toSql()}) as latest_md"))
            ->mergeBindings($latestDiagnosesSubquery)
            ->join('medical_diagnosis_diseases as mdd', 'latest_md.id', '=', 'mdd.medical_diagnosis_id')
            ->join('icd10_diseases as icd', 'mdd.icd10_disease_id', '=', 'icd.id')
            ->select(
                'icd.id',
                'icd.icd10_code',
                'icd.disease_name_en',
                DB::raw('COUNT(DISTINCT latest_md.id) as total_cases')
            )
            ->groupBy('icd.id', 'icd.icd10_code', 'icd.disease_name_en')
            ->orderBy('total_cases', 'desc')
            ->limit(4)
            ->get();

        // คำนวณจำนวนและเปอร์เซ็นต์ของ 4 โรคแรก
        $topDiseasesData = [];
        $topDiseasesTotalCases = 0;

        foreach ($topDiseases as $disease) {
            $percentage = round(($disease->total_cases / $totalDiagnoses) * 100, 2);
            $topDiseasesData[] = [
                'rank' => count($topDiseasesData) + 1,
                'icd10_code' => $disease->icd10_code,
                'disease_name' => $disease->disease_name_en,
                'total_cases' => $disease->total_cases,
                'percentage' => $percentage
            ];
            $topDiseasesTotalCases += $disease->total_cases;
        }

        // คำนวณ "อื่นๆ" - นับผู้ป่วยที่มีโรคอื่นๆ ที่ไม่ติด Top 4
        $topDiseaseIds = $topDiseases->pluck('id')->toArray();

        if (!empty($topDiseaseIds)) {
            $othersCount = DB::table(DB::raw("({$latestDiagnosesSubquery->toSql()}) as latest_md"))
                ->mergeBindings($latestDiagnosesSubquery)
                ->join('medical_diagnosis_diseases as mdd', 'latest_md.id', '=', 'mdd.medical_diagnosis_id')
                ->whereNotIn('mdd.icd10_disease_id', $topDiseaseIds)
                ->distinct('latest_md.id')
                ->count('latest_md.id');
        } else {
            $othersCount = $totalDiagnoses;
        }

        if ($othersCount > 0) {
            $othersPercentage = round(($othersCount / $totalDiagnoses) * 100, 2);
            $topDiseasesData[] = [
                'rank' => 5,
                'icd10_code' => 'OTHERS',
                'disease_name' => 'อื่นๆ',
                'total_cases' => $othersCount,
                'percentage' => $othersPercentage
            ];
        }

        return response()->json([
            'message' => 'รายงานโรคที่พบบ่อยที่สุดในเดือน ' . $this->formatThaiMonth($currentMonth),
            'auto_detected_period' => [
                'thai_month' => $this->formatThaiMonth($currentMonth),
                'month' => $currentMonth->month,
                'year' => $currentMonth->year,
                'thai_year' => $currentMonth->year + 543,
                'start_date' => $startOfMonth->format('Y-m-d'),
                'end_date' => $endOfMonth->format('Y-m-d'),
                'total_diagnoses' => $totalDiagnoses,
                'detected_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            'data' => $topDiseasesData
        ]);
    }

    // Helper method สำหรับแปลงเดือนเป็นภาษาไทย
    private function formatThaiMonth($date)
    {
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];

        $thaiYear = $date->year + 543; // แปลงเป็น พ.ศ.
        $thaiMonth = $thaiMonths[$date->month];

        return "{$thaiMonth} {$thaiYear}";
    }


    public function getTodayTreatmentStatus()
    {
        // จับวันปัจจุบันอัตโนมัติ
        $today = Carbon::now();
        $startOfDay = $today->startOfDay();
        $endOfDay = $today->copy()->endOfDay();

        // ดึงข้อมูลการนัดหมายทั้งหมดในวันนี้
        $todayAppointments = DB::table('appointment as a')
            ->leftJoin('checkin as c', 'a.id', '=', 'c.appointment_id')
            ->leftJoin('treatment as t', 'c.id', '=', 't.checkin_id')
            ->select(
                'a.id as appointment_id',
                'a.appointment_date',
                'a.status as appointment_status',
                'a.was_missed', // เพิ่มฟิลด์นี้
                'a.missed_appointment_date', // เพิ่มฟิลด์นี้
                'c.checkin_status',
                't.treatment_status',
                DB::raw('
            CASE
                WHEN a.status = "missed" THEN "missed"
                WHEN a.was_missed = 1 AND DATE(a.missed_appointment_date) = DATE(NOW()) THEN "missed"
                WHEN a.status = "scheduled" AND (c.checkin_status IS NULL OR c.checkin_status = "not-checked-in") THEN "waiting_checkin"
                WHEN a.status = "scheduled" AND c.checkin_status = "checked-in" AND (t.treatment_status IS NULL OR t.treatment_status = "not-treated") THEN "waiting_treatment"
                WHEN a.status = "completed" AND c.checkin_status = "checked-in" AND t.treatment_status = "treated" THEN "completed_treatment"
                WHEN a.status = "scheduled" AND c.checkin_status = "missed" THEN "missed"
                ELSE "other"
            END as overall_status
        ')
            )
            ->where(function ($query) use ($startOfDay, $endOfDay) {
                $query->whereBetween('a.appointment_date', [$startOfDay, $endOfDay])
                    ->orWhere(function ($subQuery) use ($startOfDay, $endOfDay) {
                        // รวมกรณีที่ was_missed = 1 และ missed_appointment_date ตรงกับวันนี้
                        $subQuery->where('a.was_missed', 1)
                            ->whereBetween('a.missed_appointment_date', [$startOfDay, $endOfDay]);
                    });
            })
            ->get();

        // นับจำนวนรวม
        $totalAppointments = $todayAppointments->count();

        // กำหนดสถานะทั้งหมดที่ต้องแสดงเสมอ
        $statusLabels = [
            'waiting_checkin' => 'ยังไม่ได้ทำการรักษา',
            'waiting_treatment' => 'อยู่ระหว่างการรักษา',
            'completed_treatment' => 'รักษาเสร็จสิ้น',
            'missed' => 'ไม่มาตามนัด',
        ];

        // จัดกลุ่มตามสถานะ (ถ้ามีข้อมูล)
        $statusGroups = $todayAppointments->groupBy('overall_status');

        // สร้าง status_breakdown ให้ครบทุกสถานะเสมอ
        $statusData = [];
        foreach ($statusLabels as $statusKey => $statusLabel) {
            $count = $statusGroups->get($statusKey, collect())->count();
            $percentage = $totalAppointments > 0 ? round(($count / $totalAppointments) * 100, 2) : 0;

            $statusData[] = [
                'status' => $statusKey,
                'status_label' => $statusLabel,
                'count' => $count,
                'percentage' => $percentage,
                'description' => $statusLabel // ← ใช้ $statusLabel โดยตรง
            ];
        }

        // ตรวจสอบว่าไม่มีการนัดหมายหรือไม่
        if ($totalAppointments == 0) {
            return response()->json([
                'message' => 'ไม่มีการนัดหมายในวันที่ ' . $this->formatThaiDate($today),
                'date_info' => [
                    'thai_date' => $this->formatThaiDate($today),
                    'date' => $today->format('Y-m-d'),
                    'day_of_week' => $this->getThaiDayOfWeek($today),
                    'detected_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                'summary' => [
                    'total_appointments' => 0,
                    'status_breakdown' => $statusData // ส่งทุกสถานะแม้ count = 0
                ]
            ]);
        }

        // กรณีมีการนัดหมาย - ส่งข้อมูลปกติ
        return response()->json([
            'message' => 'รายงานสถานะการรักษารายวันของวันที่ ' . $this->formatThaiDate($today),
            'date_info' => [
                'thai_date' => $this->formatThaiDate($today),
                'date' => $today->format('Y-m-d'),
                'day_of_week' => $this->getThaiDayOfWeek($today),
                'detected_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            'summary' => [
                'total_appointments' => $totalAppointments,
                'status_breakdown' => $statusData
            ]
        ]);
    }

    // Helper Methods (คงเดิม)
    private function getStatusLabel($status)
    {
        $labels = [
            'waiting_checkin' => 'ยังไม่ได้ทำการรักษา',
            'waiting_treatment' => 'อยู่ระหว่างการรักษา',
            'completed_treatment' => 'รักษาเสร็จสิ้น',
            'missed' => 'ไม่มาตามนัด',
            'other' => 'อื่นๆ'
        ];

        return $labels[$status] ?? 'ไม่ระบุ';
    }

    private function calculateWaitingTime($checkinTime, $treatmentTime)
    {
        if (!$checkinTime || !$treatmentTime) {
            return null;
        }

        $checkin = Carbon::parse($checkinTime);
        $treatment = Carbon::parse($treatmentTime);

        $diffInMinutes = $treatment->diffInMinutes($checkin);

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' นาที';
        } else {
            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            return $hours . ' ชม. ' . $minutes . ' นาที';
        }
    }

    private function formatThaiDate($date)
    {
        $thaiMonths = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม'
        ];

        $thaiYear = $date->year + 543;
        $thaiMonth = $thaiMonths[$date->month];
        $day = $date->day;

        return "{$day} {$thaiMonth} {$thaiYear}";
    }

    private function getThaiDayOfWeek($date)
    {
        $thaiDays = [
            0 => 'อาทิตย์',
            1 => 'จันทร์',
            2 => 'อังคาร',
            3 => 'พุธ',
            4 => 'พฤหัสบดี',
            5 => 'ศุกร์',
            6 => 'เสาร์'
        ];

        return $thaiDays[$date->dayOfWeek];
    }
}