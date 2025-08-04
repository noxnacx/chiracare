<?php

// app/Http/Controllers/AdminHospitalController.php

namespace App\Http\Controllers;
use App\Models\MedicalDiagnosis;
use App\Models\TrainingUnit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;

class StaticController extends Controller
{
    // สร้าง function สำหรับแสดงหน้า static_hospital



    public function showStaticgraph(Request $request)
    {
        $departments = ['er', 'ipd', 'opd'];
        $result = [];

        // รับช่วงวันที่จาก request หรือกำหนด default เป็นวันนี้
        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();


        foreach ($departments as $dept) {
            if ($dept === 'ipd') {
                // ✅ กรอง diagnosis ที่อยู่ภายในช่วงวัน และเลือก diagnosis ล่าสุดต่อ treatment_id
                $latestDiagnosesIds = DB::table('medical_diagnosis as md1')
                    ->select('md1.id')
                    ->join(DB::raw("
            (
                SELECT treatment_id, MAX(diagnosis_date) AS latest_date
                FROM medical_diagnosis
                WHERE department_type = 'ipd'
                AND diagnosis_date BETWEEN '$startDate' AND '$endDate'
                GROUP BY treatment_id
            ) as latest
        "), function ($join) {
                        $join->on('md1.treatment_id', '=', 'latest.treatment_id')
                            ->on('md1.diagnosis_date', '=', 'latest.latest_date');
                    })
                    ->pluck('md1.id');

                // ✅ ไม่ต้องกรองวันที่ซ้ำใน query หลัก เพราะเลือกจาก subquery ที่กรองมาแล้ว
                $topDiseases = DB::table('medical_diagnosis')
                    ->whereIn('medical_diagnosis.id', $latestDiagnosesIds)
                    ->join('medical_diagnosis_diseases', 'medical_diagnosis.id', '=', 'medical_diagnosis_diseases.medical_diagnosis_id')
                    ->join('icd10_diseases', 'medical_diagnosis_diseases.icd10_disease_id', '=', 'icd10_diseases.id')
                    ->select(
                        'icd10_diseases.disease_name_en',
                        'icd10_diseases.icd10_code',
                        DB::raw('count(distinct medical_diagnosis.treatment_id) as count')
                    )
                    ->groupBy('icd10_diseases.disease_name_en', 'icd10_diseases.icd10_code')
                    ->orderByDesc('count')
                    ->take(10)
                    ->get();
            } else {
                // ✅ ER/OPD ใช้ whereBetween ปกติ
                $topDiseases = DB::table('medical_diagnosis')
                    ->where('medical_diagnosis.department_type', $dept)
                    ->whereBetween('medical_diagnosis.diagnosis_date', [$startDate, $endDate])
                    ->join('medical_diagnosis_diseases', 'medical_diagnosis.id', '=', 'medical_diagnosis_diseases.medical_diagnosis_id')
                    ->join('icd10_diseases', 'medical_diagnosis_diseases.icd10_disease_id', '=', 'icd10_diseases.id')
                    ->select(
                        'icd10_diseases.disease_name_en',
                        'icd10_diseases.icd10_code',
                        DB::raw('count(*) as count')
                    )
                    ->groupBy('icd10_diseases.disease_name_en', 'icd10_diseases.icd10_code')
                    ->orderByDesc('count')
                    ->take(10)
                    ->get();
            }

            // ✅ จัดรูปแบบข้อมูลให้ frontend ใช้งาน
            $formatted = $topDiseases->map(function ($item) {
                return [
                    'code' => $item->icd10_code,
                    'name' => $item->disease_name_en,
                    'count' => $item->count,
                ];
            });

            $result[$dept] = $formatted;
        }

        return response()->json([
            'topDiseasesByDepartment' => $result
        ]);
    }








    public function showTreatmentStatistics(Request $request)
    {
        // เพิ่ม Log debug
        Log::info('🔍 showTreatmentStatistics called with params:', $request->all());

        $trainingUnits = DB::table('training_unit')->select('id', 'unit_name')->get();

        // รับค่าพารามิเตอร์จาก query string
        $departmentType = $request->input('department_type'); // เช่น 'opd', 'er', 'ipd'
        $date = $request->input('date');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        Log::info('📅 Filter params:', [
            'date' => $date,
            'department_type' => $departmentType,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        // เริ่มสร้าง query
        $query = DB::table('medical_diagnosis as md')
            ->join('treatment as t', 'md.treatment_id', '=', 't.id')
            ->join('checkin as c', 't.checkin_id', '=', 'c.id')
            ->join('appointment as a', 'c.appointment_id', '=', 'a.id')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->join('soldier as s', 'mr.soldier_id', '=', 's.id')
            ->whereNotNull('s.training_unit_id')
            ->whereIn('md.treatment_status', ['Admit', 'Refer', 'Discharge', 'Follow-up']); // ⚠️ แก้ไข 'Discharged' เป็น 'Discharge'

        // 🟡 กรองวันที่ (แบบเลือก 1 วัน หรือช่วงวัน)
        if ($date) {
            $query->whereDate('md.diagnosis_date', $date);
            Log::info("📅 Filtering by single date: {$date}");
        } elseif ($startDate && $endDate) {
            $query->whereBetween('md.diagnosis_date', [$startDate, $endDate]);
            Log::info("📅 Filtering by date range: {$startDate} to {$endDate}");
        }

        // 🟡 กรองแผนกถ้ามีเลือก
        if ($departmentType) {
            $query->where('md.department_type', $departmentType);
            Log::info("🏥 Filtering by department: {$departmentType}");
        }

        // ดึงข้อมูล
        $treatmentRaw = $query
            ->select(
                's.training_unit_id',
                'md.treatment_status',
                'md.treatment_id',
                'md.department_type'
            )
            ->get();

        Log::info('📊 Raw fetched diagnosis rows count: ' . $treatmentRaw->count());
        Log::info('Raw data sample:', $treatmentRaw->take(3)->toArray());

        // รวมข้อมูลแบบนับไม่ซ้ำ
        $aggregated = [];

        foreach ($treatmentRaw as $row) {
            $unitId = $row->training_unit_id;
            $status = $row->treatment_status;
            $treatmentId = $row->treatment_id;

            if (!isset($aggregated[$unitId])) {
                $aggregated[$unitId] = [
                    'Admit' => [],
                    'Refer' => 0,
                    'Discharge' => 0, // ⚠️ แก้ไข key ให้ตรงกับ Database
                    'Follow-up' => 0,
                ];
            }

            if ($status === 'Admit') {
                if (!isset($aggregated[$unitId]['Admit'][$treatmentId])) {
                    $aggregated[$unitId]['Admit'][$treatmentId] = [
                        'counted' => false,
                        'departments' => [],
                    ];
                }
                $aggregated[$unitId]['Admit'][$treatmentId]['departments'][] = $row->department_type;
            } else {
                // ⚠️ แก้ไข mapping สำหรับ status
                $mappedStatus = $status;
                if ($status === 'Discharge') {
                    $mappedStatus = 'Discharge'; // ให้ตรงกับ key ใน array
                }

                if (isset($aggregated[$unitId][$mappedStatus])) {
                    $aggregated[$unitId][$mappedStatus]++;
                }
            }
        }

        Log::info('🔄 Aggregated data:', $aggregated);

        // รวมผลตามหน่วยฝึก
        $statisticsData = $trainingUnits->map(function ($unit) use ($aggregated) {
            $unitId = $unit->id;
            $admitCount = 0;

            if (isset($aggregated[$unitId]['Admit'])) {
                foreach ($aggregated[$unitId]['Admit'] as $treatmentGroup) {
                    $admitCount++;
                }
            }

            $result = [
                'training_unit' => $unit->unit_name,
                'Admit' => $admitCount,
                'Refer' => $aggregated[$unitId]['Refer'] ?? 0,
                'Discharged' => $aggregated[$unitId]['Discharge'] ?? 0, // ⚠️ แก้ไข key mapping
                'Follow-up' => $aggregated[$unitId]['Follow-up'] ?? 0,
            ];

            Log::info("📈 Unit {$unit->unit_name} stats:", $result);

            return $result;
        });

        Log::info('✅ Final treatment statistics:', $statisticsData->toArray());

        return response()->json([
            'success' => true, // ⚠️ เพิ่ม success flag
            'statisticsData' => $statisticsData
        ]);
    }



    public function showStaticDetails(Request $request)
    {
        // รับฟิลเตอร์จาก request
        $departmentFilter = $request->input('department', 'all');
        $statusFilter = $request->input('status', 'all');
        $diagnosisDate = $request->input('diagnosis_date');
        $unitFilter = $request->input('unit_name');

        // สร้าง query ดึงข้อมูลผู้ป่วย
        $patientDetailsQuery = DB::table('medical_diagnosis as md')
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
                'tu.unit_name as training_unit_name',
                'r.rotation_name as rotation_name',
                's.affiliated_unit',
                'mr.symptom_description',
                'md.doctor_name',
                'md.training_instruction',
                'md.treatment_status',
                DB::raw('GROUP_CONCAT(DISTINCT icd.icd10_code) as icd10_codes'),
                DB::raw('GROUP_CONCAT(DISTINCT icd.disease_name_en) as disease_names'),
                'md.diagnosis_date'
            )
            ->groupBy(
                's.id',
                'md.id',
                'tu.unit_name',
                'r.rotation_name',
                's.affiliated_unit',
                'mr.symptom_description',
                'md.doctor_name',
                'md.training_instruction',
                'md.treatment_status',
                'md.diagnosis_date'
            );

        // กรองตามฟิลเตอร์
        if ($departmentFilter !== 'all') {
            $patientDetailsQuery->where('md.department_type', $departmentFilter);
        }

        if ($statusFilter !== 'all') {
            $patientDetailsQuery->where('md.treatment_status', $statusFilter);
        }

        if ($diagnosisDate) {
            $patientDetailsQuery->whereDate('md.diagnosis_date', $diagnosisDate);
        }

        if ($unitFilter) {
            $patientDetailsQuery->where('tu.unit_name', $unitFilter);
        }

        // ดึงข้อมูลที่กรองแล้ว
        $patientDetails = $patientDetailsQuery->orderBy('md.diagnosis_date', 'desc')->get();

        // ดึงชื่อหน่วยฝึกทั้งหมด
        $allUnits = DB::table('training_unit')->pluck('unit_name')->toArray();

        // คำนวณสถิติต่างๆ
        $byUnit = $patientDetails->groupBy('training_unit_name')->map->count();
        $byUnitComplete = collect($allUnits)->mapWithKeys(function ($unit) use ($byUnit) {
            return [$unit => $byUnit[$unit] ?? 0];
        });

        $byStatus = $patientDetails->groupBy('treatment_status')->map->count();
        $totalFU = $patientDetails->whereNotNull('diagnosis_date')->count();

        $summary = [
            'units' => $byUnitComplete,
            'statuses' => $byStatus,
            'total_followup' => $totalFU,
            'unit_count' => count($allUnits),
            'unit_names' => $allUnits,
        ];

        return response()->json([
            'patientDetails' => $patientDetails,
            'summary' => $summary,
        ]);
    }


    public function getDiseaseStatistics(Request $request)
    {
        // รับฟิลเตอร์จาก request
        $department = $request->input('department', 'all');
        $start_date = $request->input('start_date');  // รับวันที่เริ่มต้น
        $end_date = $request->input('end_date');      // รับวันที่สิ้นสุด

        // สร้าง query สำหรับดึงข้อมูลโรคจาก OPD และ IPD
        $query = DB::table('medical_diagnosis as md')
            ->join('medical_diagnosis_diseases as mdd', 'md.id', '=', 'mdd.medical_diagnosis_id')
            ->join('icd10_diseases as icd', 'mdd.icd10_disease_id', '=', 'icd.id')
            ->select('icd.icd10_code', 'icd.disease_name_en', DB::raw('COUNT(*) as count'))
            ->groupBy('icd.icd10_code', 'icd.disease_name_en')
            ->orderByDesc('count');

        // กรองตามประเภท OPD หรือ IPD
        if ($department != 'all') {
            $query->where('md.department_type', $department);
        }

        // กรองตามวันที่เริ่มต้นและสิ้นสุด
        if ($start_date) {
            $query->where('md.diagnosis_date', '>=', $start_date);
        }

        if ($end_date) {
            $query->where('md.diagnosis_date', '<=', $end_date);
        }

        // ดึงข้อมูลโรค 10 อันดับแรก
        $topDiseases = $query->limit(10)->get();

        return response()->json([
            'topDiseases' => $topDiseases
        ]);
    }









    public function tableStaticAdminHospital(Request $request)
    {
        $date = $request->input('date') ?? now()->toDateString();
        Log::info("🔍 กำลังดึงข้อมูลวันที่: {$date}");

        $medicalDiagnoses = MedicalDiagnosis::with([
            'treatment.checkin.appointment.medicalReport.soldier.trainingUnit',
            'treatment.checkin.appointment',
            'diseases'
        ])
            ->whereDate('diagnosis_date', $date)
            ->get();

        Log::info("📊 พบ Medical Diagnoses จำนวน: " . $medicalDiagnoses->count());

        $unitNames = TrainingUnit::pluck('unit_name')->toArray();
        $unitSummaries = array_fill_keys($unitNames, 0);
        $admitSummaries = array_fill_keys($unitNames, 0);
        $statusCounts = [
            'admit' => 0,
            'discharge' => 0,
            'refer' => 0,
            'followup' => 0,
        ];

        $unitDistinctAdmitMap = [];
        $unitFullPatientNameMap = [];
        $unitSoldierIdMap = [];

        foreach ($medicalDiagnoses as $diagnosis) {
            // ตรวจสอบ relationship ทีละขั้น
            if (
                !$diagnosis->treatment ||
                !$diagnosis->treatment->checkin ||
                !$diagnosis->treatment->checkin->appointment ||
                !$diagnosis->treatment->checkin->appointment->medicalReport ||
                !$diagnosis->treatment->checkin->appointment->medicalReport->soldier
            ) {

                Log::warning("❌ Diagnosis {$diagnosis->id} ข้อมูล relationship ไม่ครบ");
                continue;
            }

            $soldier = $diagnosis->treatment->checkin->appointment->medicalReport->soldier;
            $unit = optional($soldier->trainingUnit)->unit_name ?? 'ไม่ระบุหน่วย';
            $soldierName = trim($soldier->first_name . ' ' . $soldier->last_name);
            $soldierId = $soldier->id;

            // เพิ่มหน่วยใหม่ถ้ายังไม่มี
            if (!array_key_exists($unit, $unitSummaries)) {
                $unitSummaries[$unit] = 0;
                $admitSummaries[$unit] = 0;
                $unitFullPatientNameMap[$unit] = [];
                $unitSoldierIdMap[$unit] = [];
            }

            $unitSummaries[$unit]++;

            // ตรวจสอบ treatment_status
            Log::info("📋 Processing - Unit: {$unit}, Status: {$diagnosis->treatment_status}, Soldier: {$soldierName}");

            switch ($diagnosis->treatment_status) {
                case 'Discharge':
                    $statusCounts['discharge']++;
                    break;
                case 'Refer':
                    $statusCounts['refer']++;
                    break;
                case 'Follow-up':
                    $statusCounts['followup']++;
                    break;
            }

            // จัดการ Patient Names
            if ($diagnosis->treatment_status === 'Admit') {
                if (!isset($unitFullPatientNameMap[$unit][$soldierName])) {
                    $unitFullPatientNameMap[$unit][$soldierName] = true;
                    $unitSoldierIdMap[$unit][$soldierName] = $soldierId;
                }
            } else {
                $unitFullPatientNameMap[$unit][$soldierName] = true;
                $unitSoldierIdMap[$unit][$soldierName] = $soldierId;
            }

            // จัดการ Admit
            if ($diagnosis->treatment_status === 'Admit') {
                $key = $diagnosis->treatment_id . '|' . $diagnosis->diagnosis_date;

                if (!isset($unitDistinctAdmitMap[$unit][$key])) {
                    $unitDistinctAdmitMap[$unit][$key] = [];
                }
                $unitDistinctAdmitMap[$unit][$key][] = $diagnosis->department_type;
            }
        }

        // คำนวณ Admit Summary แยกต่างหน่วย
        $unitPatientCount = [];
        $totalUniquePatients = 0;
        $unitDistinctTreatmentDisplay = [];
        $unitFullPatientCount = [];

        foreach ($unitNames as $unit) {
            $unitPatientCount[$unit] = 0;
            $unitFullPatientCount[$unit] = isset($unitFullPatientNameMap[$unit]) ? count($unitFullPatientNameMap[$unit]) : 0;
            $unitDistinctTreatmentDisplay[$unit] = [];

            $unitAdmitKeys = []; // แยกต่างหน่วย

            if (isset($unitDistinctAdmitMap[$unit])) {
                foreach ($unitDistinctAdmitMap[$unit] as $key => $departments) {
                    if (!in_array($key, $unitAdmitKeys)) {
                        $admitSummaries[$unit]++;
                        $statusCounts['admit']++;
                        $unitAdmitKeys[] = $key;
                    }

                    $uniqueDepts = collect($departments)->unique()->values()->toArray();
                    $unitDistinctTreatmentDisplay[$unit][] = $uniqueDepts;
                    $unitPatientCount[$unit]++;
                    $totalUniquePatients++;

                    Log::info("📌 Admit Summary → unit={$unit}, key={$key}, departments=" . json_encode($uniqueDepts));
                }
            }
        }

        Log::info("✅ สรุปผลลัพธ์:", [
            'total_diagnoses' => $medicalDiagnoses->count(),
            'status_counts' => $statusCounts,
            'total_unique_patients' => $totalUniquePatients
        ]);

        $followUpAppointments = Appointment::where('is_follow_up', 1)->get();

        return view('admin-hospital.static_hospital', [
            'medicalDiagnoses' => $medicalDiagnoses,
            'date' => $date,
            'unitSummaries' => $unitSummaries,
            'admitSummaries' => $admitSummaries,
            'statusCounts' => $statusCounts,
            'followUpAppointments' => $followUpAppointments,
            'unitDistinctTreatmentDisplay' => $unitDistinctTreatmentDisplay,
            'totalUniquePatients' => $totalUniquePatients,
            'unitNames' => $unitNames,
            'unitPatientCount' => $unitPatientCount,
            'unitFullPatientCount' => $unitFullPatientCount,
        ]);
    }



}
