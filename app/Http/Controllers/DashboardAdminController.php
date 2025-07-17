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
        // 🗓 ดึงข้อมูลนัดหมายวันนี้ทั้งหมด
        $appointments = Appointment::whereDate('appointment_date', Carbon::today())
            ->with(['medicalReport.soldier', 'checkin'])
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


        // 🏥 ดึงผู้ป่วย Admit ใน IPD (จาก medical_diagnosis)
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
        $ipdAdmitCount = $admitPatients->count();


        return view('admin-hospital.dashboardadmin', [
            'appointments' => $appointments,
            'criticalAppointments' => $criticalAppointments,
            'opdCount' => $opdCount,
            'erCount' => $erCount,
            'ipdCount' => $ipdCount,
            'admitPatients' => $admitPatients,
            'ipdAdmitCount' => $ipdAdmitCount // ✅ เพิ่มตัวแปรนี้

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
            $codes = array_map('trim', explode(',', $request->input('codes')));
            $start = $request->input('start');
            $end = $request->input('end');

            $query = DB::table('medical_diagnosis')
                ->join('medical_diagnosis_diseases', 'medical_diagnosis.id', '=', 'medical_diagnosis_diseases.medical_diagnosis_id')
                ->join('icd10_diseases', 'medical_diagnosis_diseases.icd10_disease_id', '=', 'icd10_diseases.id')
                ->whereIn('icd10_diseases.icd10_code', $codes);

            if ($start && $end) {
                $query->whereBetween(DB::raw('DATE(medical_diagnosis.diagnosis_date)'), [$start, $end]);
            }

            $diseasesData = $query
                ->select(
                    'icd10_diseases.disease_name_en as name',
                    'icd10_diseases.icd10_code as disease_code',
                    DB::raw('count(*) as count')
                )
                ->groupBy('icd10_diseases.disease_name_en', 'icd10_diseases.icd10_code')
                ->get();

            return response()->json($diseasesData);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }








    public function goToDashboard()
    {



        return view('opd.dashboard_opd');
    }

}




