<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use Carbon\Carbon;
use App\Models\MedicalReport;
use App\Models\Icd10Disease;

use App\Models\TrainingUnit;
use App\Models\Soldier;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingUnitController extends Controller
{
    public function totalTrainingUnit()
    {
        // Count the total number of training units
        $totalTrainingUnits = TrainingUnit::count();

        // Pass data to the view
        return view('admin.dashboardadmin', compact('totalTrainingUnits'));
    }


    public function show_trainingunit_page()
    {
        $units = TrainingUnit::all();
        return view('admin.rotation_training', compact('units'));
    }





    // เพิ่ม Training Unit ใหม่ (ค่าเริ่มต้น status เป็น 'active')
    public function store_trainingunit(Request $request)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
        ]);

        TrainingUnit::create([
            'unit_name' => $request->unit_name,
            'status' => 'active', // ค่าเริ่มต้นเป็น 'active'
        ]);

        return redirect('/rotation_training')->with('success', 'Training Unit added successfully!');
    }


    // อัปเดตข้อมูล Training Unit
    public function update_trainingunit(Request $request, $id)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'status' => 'in:active,inactive',
        ]);

        $unit = TrainingUnit::findOrFail($id);
        $unit->update([
            'unit_name' => $request->unit_name,
            'status' => $request->status,
        ]);

        return redirect('/rotation_training')->with('success', 'Training Unit updated successfully!');
    }

    // ลบ Training Unit
    public function delete_trainingunit($id)
    {
        $unit = TrainingUnit::findOrFail($id);
        $unit->delete();

        return redirect('/rotation_training')->with('success', 'Training Unit deleted successfully!');
    }

    // เปลี่ยนสถานะ Training Unit (active ↔ inactive)
    public function toggle_status_trainingunit($id)
    {
        $unit = TrainingUnit::findOrFail($id);
        $unit->status = ($unit->status == 'active') ? 'inactive' : 'active';
        $unit->save();

        return redirect('/rotation_training')->with('success', 'Status updated successfully!');
    }


    public function edit_trainingunit($id)
    {
        $unit = TrainingUnit::find($id);

        if (!$unit) {
            return response()->json(['error' => 'ไม่พบข้อมูล'], 404);
        }

        return response()->json($unit);
    }

    public function fetchTrainingUnit()
    {
        // ✅ ดึงข้อมูลหน่วยฝึกทั้งหมด
        $units = TrainingUnit::all();

        // ✅ ส่งข้อมูลไปยัง View
        return view('trainingUnit.training_units', compact('units'));
    }


    
    // แสดงแดชบอร์ดของหน่วยฝึกที่เลือก
    public function dashboardTrainingUnit($id, Request $request)
    {
        try {
            $unit = TrainingUnit::findOrFail($id);

            // นับจำนวนทหารในหน่วยฝึก
            $totalSoldiers = Soldier::where('training_unit_id', $id)->count();

            // ดึงจำนวนของแต่ละสถานะที่เกี่ยวข้องกับหน่วยฝึกนี้
            $statusCounts = MedicalReport::whereHas('soldier', function ($query) use ($id) {
                $query->where('training_unit_id', $id);
            })
                ->selectRaw("status, COUNT(*) as count")
                ->groupBy('status')
                ->pluck('count', 'status');

            // ตรวจสอบว่ามีค่าหรือไม่ ถ้าไม่มีให้ใช้ 0
            $sentCount = $statusCounts['sent'] ?? 0;
            $pendingCount = $statusCounts['pending'] ?? 0;
            $approvedCount = $statusCounts['approved'] ?? 0;
            $rejectedCount = $statusCounts['rejected'] ?? 0;

            // ดึงข้อมูลการนัดหมายของวันนี้
            $appointments = Appointment::whereIn('status', ['approved', 'scheduled'])
                ->whereDate('appointment_date', Carbon::today())
                ->whereHas('medicalReport.soldier', function ($query) use ($id) {
                    $query->where('training_unit_id', $id);
                })
                ->select(['id', 'medical_report_id', 'appointment_date', 'appointment_location', 'case_type', 'status', 'created_at'])
                ->with(['medicalReport.soldier:id,first_name,last_name'])
                ->get();

            // ดึงข้อมูลทหารทั้งหมดที่เกี่ยวข้องกับหน่วยฝึก พร้อมกับความสัมพันธ์
            $soldiers = Soldier::where('training_unit_id', $id)
                ->with([
                    'medicalReports.medicalDiagnoses.diseases', // ดึงข้อมูลโรคจากการวินิจฉัย
                ])
                ->get();

            // สร้าง array สำหรับเก็บชื่อโรคและจำนวน
            $diseaseCounts = [];

            // นับจำนวนโรคที่พบในทหาร
            foreach ($soldiers as $soldier) {
                foreach ($soldier->medicalReports as $medicalReport) {
                    foreach ($medicalReport->medicalDiagnoses as $diagnosis) {
                        foreach ($diagnosis->diseases as $disease) {
                            $diseaseName = $disease->disease_name_en; // ดึงชื่อโรคจาก ICD10Disease
                            if (isset($diseaseCounts[$diseaseName])) {
                                $diseaseCounts[$diseaseName]++;
                            } else {
                                $diseaseCounts[$diseaseName] = 1;
                            }
                        }
                    }
                }
            }

            // เรียงลำดับโรคจากจำนวนมากที่สุด
            arsort($diseaseCounts);

            // ดึง 5 อันดับแรก
            $topDiseases = array_slice($diseaseCounts, 0, 5);

            // จัดรูปแบบข้อมูลให้พร้อมสำหรับ Blade view
            $formattedTopDiseases = [];
            foreach ($topDiseases as $diseaseName => $count) {
                $formattedTopDiseases[] = ['name' => $diseaseName, 'count' => $count];
            }

            // ส่งข้อมูลทั้งหมดไปยัง Blade view
            return view('trainingUnit.dashboard_trainingunit', [
                'unit' => $unit,
                'totalSoldiers' => $totalSoldiers,
                'sentCount' => $sentCount,
                'pendingCount' => $pendingCount,
                'approvedCount' => $approvedCount,
                'rejectedCount' => $rejectedCount,
                'appointments' => $appointments,
                'topDiseases' => $formattedTopDiseases,

                // ส่งข้อมูลโรคที่พบมากที่สุด
            ]);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error fetching dashboard data: ' . $e->getMessage());

            // Optionally, you can return a custom error message
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }





}
