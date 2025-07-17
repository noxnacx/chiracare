<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalReport;
use App\Models\Soldier;
use App\Models\TrainingUnit;
use App\Models\MedicalReportImage;
use Illuminate\Support\Facades\Log;

use App\Models\Appointment;

use App\Models\VitalSign;

class MedicalReportController extends Controller
{
    // ✅ แสดงฟอร์มเลือกทหารและบันทึกค่าทางการแพทย์

    public function showMedicalReportForm($id)
    {
        $unit = TrainingUnit::findOrFail($id);
        $soldiers = Soldier::where('training_unit_id', $id)->get();

        return view('trainingUnit.create_medicalReport', compact('unit', 'soldiers'));
    }



    public function saveMedicalReport(Request $request)
    {
        try {
            // ✅ ตรวจสอบค่าที่จำเป็น
            $request->validate([
                'soldier_id' => 'required|exists:soldier,id',
                'symptom_description' => 'required|string',
                'pain_score' => 'nullable|integer|min:0|max:10',
                'temperature' => 'nullable|numeric|min:30|max:45',
                'blood_pressure' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
                'heart_rate' => 'nullable|integer|min:40|max:180',
                'atk_test_results' => 'nullable|array',
                'atk_test_results.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'symptom_images' => 'nullable|array',
                'symptom_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // ✅ ป้องกัน Error หาก blood_pressure ไม่มีค่า
            $systolic = null;
            $diastolic = null;
            if (!empty($request->blood_pressure) && strpos($request->blood_pressure, "/") !== false) {
                list($systolic, $diastolic) = explode("/", $request->blood_pressure);
            }

            // ✅ ตรวจสอบระดับความเสี่ยง (ถ้ามีค่า blood_pressure)
            $riskLevel = 'green'; // ค่าเริ่มต้น
            if ($systolic !== null && $diastolic !== null) {
                $riskLevel = $this->calculateRiskLevel((int) $systolic, (int) $diastolic, $request->temperature);
            }

            // ✅ ถ้าระดับความเสี่ยงเป็น 'yellow' หรือ 'red' ให้ตั้งค่า status เป็น 'in ER'
            $status = 'pending'; // ถ้าไม่ได้กรอก risk level หรือเป็น green ตั้งค่าเป็น 'pending'
            if ($riskLevel === 'yellow' || $riskLevel === 'red') {
                $status = 'in ER';
            }

            // ✅ บันทึกค่าชีวิต (vital signs) (แต่สามารถเป็น null ได้)
            $vitalSign = VitalSign::create([
                'temperature' => $request->temperature ?? null,
                'blood_pressure' => $request->blood_pressure ?? null,
                'heart_rate' => $request->heart_rate ?? null,
                'source' => 'appointment',
                'risk_level' => $riskLevel,
            ]);

            // ✅ บันทึก Medical Report
            $report = MedicalReport::create([
                'soldier_id' => $request->soldier_id,
                'symptom_description' => $request->symptom_description,
                'pain_score' => $request->pain_score ?? null,
                'vital_signs_id' => $vitalSign->id ?? null,
                'status' => $status, // ตั้งค่า status เป็น 'in ER' หากความเสี่ยงเป็น 'yellow' หรือ 'red', หรือ 'pending' ถ้าเป็น green หรือไม่ได้กรอก
            ]);

            Log::info('Medical Report Created Successfully: ', ['report' => $report]);

            // ✅ บันทึกหลายรูป ATK
            if ($request->hasFile('atk_test_results')) {
                foreach ($request->file('atk_test_results') as $file) {
                    $atkFilename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/medical_reports/atk'), $atkFilename);

                    MedicalReportImage::create([
                        'medical_report_id' => $report->id,
                        'image_type' => 'atk',
                        'image_symptom' => 'uploads/medical_reports/atk/' . $atkFilename,
                    ]);
                }
            }

            // ✅ บันทึกหลายรูปอาการ
            if ($request->hasFile('symptom_images')) {
                foreach ($request->file('symptom_images') as $file) {
                    $symptomFilename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/medical_reports/symptoms'), $symptomFilename);

                    MedicalReportImage::create([
                        'medical_report_id' => $report->id,
                        'image_type' => 'symptom',
                        'image_symptom' => 'uploads/medical_reports/symptoms/' . $symptomFilename,
                    ]);
                }
            }

            // ✅ เปลี่ยนเส้นทางไปยัง wait_appointment พร้อมข้อความ success
            return response()->json([
                'success' => true,
                'message' => 'บันทึกข้อมูลสำเร็จ!หกก',
                'redirect' => route('wait_appointment') // ✅ ส่ง URL กลับไป
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving medical report: ' . $e->getMessage());

            // ✅ ถ้าผิดพลาดให้ redirect กลับและแสดง error message
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด! ' . $e->getMessage());
        }
    }





    // ✅ ฟังก์ชันคำนวณ Risk Level ตามค่าชีวิต
    private function calculateRiskLevel($systolic, $diastolic, $temperature)
    {
        // ✅ ตรวจสอบอุณหภูมิร่างกายก่อน (มีผลต่อระดับความเสี่ยง)
        if ($temperature > 40) {
            return 'red'; // 🔴 ฉุกเฉิน ไข้สูงเกิน 40°C
        } elseif ($temperature > 38) {
            return 'yellow'; // 🟡 เฝ้าระวัง ไข้เกิน 38°C
        }

        // ✅ ตรวจสอบค่าความดันโลหิต
        if ($systolic >= 180 || $diastolic >= 120) {
            return 'red'; // 🔴 ฉุกเฉิน Hypertensive Crisis
        } elseif ($systolic >= 140 || $diastolic >= 90) {
            return 'red'; // 🔴 อันตราย Hypertension Stage 2
        } elseif ($systolic >= 121 || $diastolic >= 81) {
            return 'yellow'; // 🟡 เฝ้าระวัง Hypertension Stage 1
        } elseif ($systolic < 90 || $diastolic < 60) {
            return 'yellow'; // 🟡 ความดันต่ำ
        }

        return 'green'; // 🟢 ปกติ
    }
    public function showWaitAppointment()
    {
        // ดึงข้อมูล medicalReports พร้อม appointment, soldier, และ images
        $medicalReports = MedicalReport::with(['appointment', 'soldier', 'images'])->get();

        // Log ข้อมูลของ appointment เพื่อดูค่าของ status และ case_type
        foreach ($medicalReports as $report) {
            Log::info('Appointment Data:', [
                'status' => $report->appointment->status ?? 'ไม่มีข้อมูลสถานะ',
                'case_type' => $report->appointment->case_type ?? 'ไม่มีข้อมูลประเภทเคส',
            ]);
        }

        // ตรวจสอบว่ามีรายการใดที่มีนัดหมายในสถานะ scheduled หรือไม่
        $hasScheduled = $medicalReports->contains(function ($report) {
            return $report->appointment && $report->appointment->status === 'scheduled';
        });

        // ส่งข้อมูลไปยัง view
        return view('trainingUnit.wait_appointment', compact('medicalReports', 'hasScheduled'));
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'status' => 'required|string'
        ]);

        try {
            MedicalReport::whereIn('id', $request->ids)->update(['status' => $request->status]);

            return response()->json(["status" => "success"]);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }



    public function waitAppointment(Request $request)
    {
        $selectedStatus = $request->query('status', 'pending');

        $query = MedicalReport::with(['soldier.rotation', 'soldier.trainingUnit', 'appointment']);

        if ($selectedStatus === 'scheduled') {
            // ดึงเฉพาะ MedicalReport ที่มี appointment และ status = scheduled
            $query->whereHas('appointment', function ($q) {
                $q->where('status', 'scheduled');
            });
        } else {
            // ใช้ medical_report.status ปกติ
            $query->where('status', $selectedStatus);
        }

        $medicalReports = $query->orderBy('created_at', 'desc')->get();

        return view('trainingUnit.wait_appointment', [
            'medicalReports' => $medicalReports,
            'selectedStatus' => $selectedStatus,
        ]);
    }


    public function sentAppointments()
    {
        // ดึงข้อมูลที่มีสถานะเป็น 'sent' เท่านั้น
        $medicalReports = MedicalReport::whereRaw("LOWER(status) = 'sent'")->get();

        // ส่งตัวแปรไปยัง View
        return view('trainingUnit.wait_hospital_appointment', compact('medicalReports'));
    }


    public function getMedicalReport($id)
    {
        $medicalReport = MedicalReport::with('images', 'soldier', 'vitalSign')->find($id);

        if (!$medicalReport) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล']);
        }

        return response()->json([
            'success' => true,
            'soldier_name' => $medicalReport->soldier->first_name . ' ' . $medicalReport->soldier->last_name,
            'soldier_unit' => $medicalReport->soldier->affiliated_unit,
            'soldier_rotation' => $medicalReport->soldier->rotation->rotation_name ?? '-',
            'soldier_training' => $medicalReport->soldier->training_unit,
            'temperature' => $medicalReport->vitalSign->temperature ?? '-',
            'blood_pressure' => $medicalReport->vitalSign->blood_pressure ?? '-',
            'heart_rate' => $medicalReport->vitalSign->heart_rate ?? '-',
            'pain_score' => $medicalReport->pain_score ?? '-',
            'symptom_description' => $medicalReport->symptom_description ?? 'ไม่มีข้อมูล',
            'risk_level' => $medicalReport->vitalSign->risk_level, // เพิ่มการส่งข้อมูล risk_level

            'images' => [
                'atk' => $medicalReport->images->where('image_type', 'atk')->pluck('image_symptom')->map(fn($image) => asset($image))->toArray(),
                'symptom' => $medicalReport->images->where('image_type', 'symptom')->pluck('image_symptom')->map(fn($image) => asset($image))->toArray(),
            ]
        ]);
    }





    // ✅ ฟังก์ชันดึงข้อมูลแดชบอร์ดของหน่วยฝึก


}

