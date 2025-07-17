<?php

// app/Http/Controllers/AdminHospitalController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaticController extends Controller
{
    // สร้าง function สำหรับแสดงหน้า static_hospital
    public function showStaticHospital()
    {
        return view('admin-hospital.static_hospital');
    }



    public function showStaticgraph(Request $request)
    {
        // ดึงข้อมูล 10 โรคที่พบมากที่สุดจากฐานข้อมูล
        $topDiseases = DB::table('medical_diagnosis')
            ->join('medical_diagnosis_diseases', 'medical_diagnosis.id', '=', 'medical_diagnosis_diseases.medical_diagnosis_id')
            ->join('icd10_diseases', 'medical_diagnosis_diseases.icd10_disease_id', '=', 'icd10_diseases.id')
            ->select('icd10_diseases.disease_name_en', 'icd10_diseases.icd10_code', DB::raw('count(*) as count'))
            ->groupBy('icd10_diseases.disease_name_en', 'icd10_diseases.icd10_code')
            ->orderByDesc('count')
            ->take(10) // ดึงแค่ 10 อันดับโรคที่พบมากที่สุด
            ->get();

        // เตรียมข้อมูลสำหรับส่งไปยัง frontend
        $topDiseasesData = $topDiseases->mapWithKeys(function ($item) {
            return [
                $item->icd10_code => [
                    'name' => $item->disease_name_en,
                    'count' => $item->count
                ]
            ];
        });

        // ส่งข้อมูลเป็น JSON ไปยัง frontend
        return response()->json([
            'topDiseasesData' => $topDiseasesData
        ]);
    }





    public function showTreatmentStatistics(Request $request)
    {
        // ดึงข้อมูลหน่วยฝึกทั้งหมดจากตาราง 'training_unit'
        $trainingUnits = DB::table('training_unit')->select('id', 'unit_name')->get();

        // ดึงข้อมูลการรักษาตาม 'training_unit' และ 'treatment_status' โดย JOIN กับ 'medical_report' และ 'soldier'
        $treatmentStatistics = DB::table('medical_diagnosis as md')
            ->join('medical_report as mr', 'md.medical_report_id', '=', 'mr.id') // JOIN กับ 'medical_report' โดยใช้ 'medical_report_id'
            ->join('soldier as s', 'mr.soldier_id', '=', 's.id') // JOIN กับ 'soldier' โดยใช้ 'soldier_id'
            ->select('s.training_unit_id', 'md.treatment_status', DB::raw('COUNT(*) as total_patients'))
            ->groupBy('s.training_unit_id', 'md.treatment_status') // กลุ่มตาม 'training_unit_id' และ 'treatment_status'
            ->get();

        // เตรียมข้อมูลสำหรับแสดงผล
        $statisticsData = $trainingUnits->map(function ($unit) use ($treatmentStatistics) {
            // กำหนดค่า default เป็น 0 สำหรับสถานะต่าง ๆ
            $statuses = [
                'Admit' => 0,
                'Refer' => 0,
                'Discharged' => 0,
                'Followup' => 0
            ];

            // หาค่าจำนวนผู้ป่วยในแต่ละสถานะการรักษาในแต่ละหน่วยฝึก
            $unitStats = $treatmentStatistics->filter(function ($item) use ($unit) {
                return $item->training_unit_id === $unit->id; // เปรียบเทียบ 'training_unit_id' กับ 'id' ของหน่วยฝึก
            });

            // เติมข้อมูลสถานะการรักษา
            foreach ($unitStats as $stat) {
                if (array_key_exists($stat->treatment_status, $statuses)) {
                    $statuses[$stat->treatment_status] = $stat->total_patients;
                }
            }

            return [
                'training_unit' => $unit->unit_name, // แสดงชื่อหน่วยฝึก
                'Admit' => $statuses['Admit'],
                'Refer' => $statuses['Refer'],
                'Discharged' => $statuses['Discharged'],
                'Followup' => $statuses['Followup']
            ];
        });

        // ส่งข้อมูลเป็น JSON
        return response()->json([
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




}
