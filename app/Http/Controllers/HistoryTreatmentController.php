<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MedicalDiagnosis;
use Illuminate\Support\Facades\Log;

class HistoryTreatmentController extends Controller
{



    public function showHospitalhistoryDetails(Request $request)
    {
        // รับค่าตัวกรองจากแบบฟอร์มหรือ query string
        $departmentFilter = $request->input('department', 'all');
        $statusFilter = $request->input('status', 'all');
        $dateFilter = $request->input('date_filter', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $today = now()->toDateString();

        // ✅ คำนวณยอดเจ็บป่วยสะสม (ไม่กรองวันที่)
        $patientsStatistics = DB::table('medical_diagnosis')
            ->select('department_type', 'treatment_status', DB::raw('COUNT(*) as total_patients'))
            ->groupBy('department_type', 'treatment_status')
            ->get();

        // ✅ คำนวณยอดเจ็บป่วย "เฉพาะวันนี้" เท่านั้น (ไม่ขึ้นกับ filter)
        $patientsStatisticsDaily = DB::table('medical_diagnosis')
            ->select('department_type', 'treatment_status', DB::raw('COUNT(*) as total_patients'))
            ->whereDate('diagnosis_date', $today)
            ->groupBy('department_type', 'treatment_status')
            ->get();

        // ✅ สร้าง query ดึงรายละเอียดผู้ป่วย (พร้อม JOIN ตารางที่เกี่ยวข้อง)
        $patientQuery = DB::table('medical_diagnosis as md')
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
                'md.treatment_status',
                'md.diagnosis_date'
            )
            ->orderBy('md.diagnosis_date', 'desc');

        // ✅ กรองวันที่ (today / custom)
        if ($dateFilter === 'today') {
            $patientQuery->whereDate('md.diagnosis_date', $today);
        } elseif ($dateFilter === 'custom' && $startDate && $endDate) {
            $patientQuery->whereBetween('md.diagnosis_date', [$startDate, $endDate . ' 23:59:59']);
        }

        // ✅ กรองแผนก
        if ($departmentFilter !== 'all') {
            $patientQuery->where('md.department_type', $departmentFilter);
        }

        // ✅ กรองสถานะ
        if ($statusFilter !== 'all') {
            $patientQuery->where('md.treatment_status', $statusFilter);
        }

        // ✅ ดึงข้อมูลจริง
        $patientDetails = $patientQuery->get();

        // ✅ ส่งค่ากลับไปยัง view
        return view('admin-hospital.history_hospital', compact(
            'patientDetails',
            'patientsStatistics',
            'patientsStatisticsDaily',
            'dateFilter',
            'statusFilter',
            'departmentFilter'
        ));
    }






}