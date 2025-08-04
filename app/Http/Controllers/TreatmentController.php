<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MedicalReport;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Treatment;
use App\Models\Checkin;
use App\Models\ICD10Disease;
use App\Models\MedicalDiagnosis;
use App\Models\VitalSign;
use Carbon\Carbon;

class TreatmentController extends Controller
{
    /**
     * ดึงรายการทหารทั้งหมด พร้อมข้อมูลการเช็คอินและสถานะการรักษา
     */
    public function getAllSoldiersTreatmentStatus()
    {
        $treatments = Treatment::with('checkin')->get();

        return response()->json($treatments);
    }

    /**
     * ดึงข้อมูลการรักษาของทหารรายบุคคล โดยใช้ ID
     */
    public function getSoldierTreatmentById($id)
    {
        $treatment = Treatment::with('checkin')->find($id);

        if (!$treatment) {
            return response()->json(['message' => 'ไม่พบข้อมูลการรักษา'], 404);
        }

        return response()->json($treatment);
    }

    /**
     * อัปเดตสถานะการรักษาของทหาร (เปลี่ยนจาก 'not-treated' เป็น 'treated')
     */
    public function markSoldierAsTreated($id)
    {
        $treatment = Treatment::find($id);

        if (!$treatment) {
            return response()->json(['message' => 'ไม่พบข้อมูลการรักษา'], 404);
        }

        // ค้นหาข้อมูล appointment ที่เชื่อมโยงกับ treatment นี้
        $appointment = $treatment->checkin->appointment;

        // ตรวจสอบว่า appointment มีสถานะ 'completed'
        if ($appointment && $appointment->status === 'completed') {
            // เปลี่ยนสถานะ treatment เป็น "treated" สำหรับทุกสถานะ
            $treatment->treatment_status = 'treated';
            $treatment->save();

            return response()->json([
                'message' => 'สถานะการรักษาเป็น treated หลังจากนัดหมายเสร็จสิ้น',
                'data' => $treatment
            ]);
        } else {
            return response()->json(['message' => 'การนัดหมายยังไม่เสร็จสิ้น'], 400);
        }
    }

    /**
     * ตรวจสอบว่าสถานะการเช็คอินของทหารเป็นอย่างไร
     */
    public function getSoldierCheckinStatus($id)
    {
        $treatment = Treatment::with('checkin')->find($id);

        if (!$treatment) {
            return response()->json(['message' => 'ไม่พบข้อมูลการรักษา'], 404);
        }

        $checkinStatus = $treatment->checkin ? $treatment->checkin->checkin_status : 'not-checked-in';

        return response()->json([
            'message' => 'สถานะการเช็คอินของทหาร',
            'checkin_status' => $checkinStatus
        ]);
    }

    public function viewCheckin()
    {
        $appointments = Appointment::with(['medicalReport.soldier', 'checkin.treatment'])
            ->whereDate('appointment_date', now()) // เฉพาะวันนี้
            ->where('appointment_location', 'OPD') // ✅ เฉพาะ location = OPD
            ->where('status', '!=', 'completed')
            ->whereHas('checkin.treatment', function ($query) {
                $query->where('checkin_status', 'checked-in');
                $query->where('treatment_status', '!=', 'treated'); // ยังไม่ได้รักษา
            })
            ->get();

        // ตรวจสอบว่ามี Medical Report หรือไม่
        $report = $appointments->first()->medicalReport ?? null;

        return view('opd.view_checkin', compact('appointments', 'report'));
    }

    public function showDiagnosisForm(Request $request)
    {
        $treatmentId = $request->input('treatmentId');

        $treatment = Treatment::with('checkin.appointment.medicalReport.soldier', 'checkin.appointment.medicalReport.vitalSign')
            ->find($treatmentId);

        $isFollowUp = false;
        $followUpAppointment = null;

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

            // ✅ ตรวจสอบว่ามี follow-up appointment ที่ยังไม่ดำเนินการ
            $followUpAppointment = Appointment::where('medical_report_id', $treatment->checkin->appointment->medicalReport->id)
                ->where('is_follow_up', true)
                ->where('status', 'scheduled')
                ->latest('appointment_date')
                ->first();

            $isFollowUp = $followUpAppointment !== null;

            // ✅ บันทึกลง log เพื่อตรวจสอบ
            if ($followUpAppointment) {
                Log::info('พบการนัดหมาย Follow-up', [
                    'treatment_id' => $treatmentId,
                    'appointment_id' => $followUpAppointment->id,
                    'appointment_date' => $followUpAppointment->appointment_date,
                    'appointment_location' => $followUpAppointment->appointment_location,
                    'case_type' => $followUpAppointment->case_type,
                ]);
            } else {
                Log::info("ไม่มีการนัดหมาย Follow-up สำหรับ treatment_id: {$treatmentId}");
            }
        } else {
            $soldierName = 'ไม่พบข้อมูลทหาร';
            $soldierUnit = $soldierRotation = $soldierTraining = 'ไม่พบข้อมูล';
            $temperature = $bloodPressure = $heartRate = '-';

            Log::warning("ไม่พบข้อมูล treatment หรือ medical report สำหรับ treatment_id: {$treatmentId}");
        }

        return view('opd.diagnosis-form', compact(
            'soldierName',
            'soldierUnit',
            'soldierRotation',
            'soldierTraining',
            'temperature',
            'bloodPressure',
            'heartRate',
            'treatmentId',
            'isFollowUp',
            'followUpAppointment'
        ));
    }
    public function updateVitalSign(Request $request, $treatmentId)
    {
        // ค้นหาข้อมูล Treatment
        $treatment = Treatment::find($treatmentId);
        $vitalSign = $treatment->checkin->appointment->medicalReport->vitalSign;
        if (!$treatment) {
            return response()->json(['message' => 'ไม่พบข้อมูลการรักษา'], 404);
        }
        // ค้นหาและอัปเดต VitalSign

        if (!$vitalSign) {
            return response()->json(['message' => 'ไม่พบข้อมูล Vital Signs'], 404);
        }

        // อัปเดตข้อมูล VitalSign
        $vitalSign->update([
            'temperature' => $request->temperature,
            'blood_pressure' => $request->blood_pressure,
            'heart_rate' => $request->heart_rate,
            'recorded_at' => now(),
        ]);

        return response()->json(['message' => 'อัปเดตข้อมูล Vital Signs สำเร็จ'], 200);
    }

    public function createFollowUpMedicalReportAndAppointment($treatmentId, Request $request)
    {
        // ค้นหาข้อมูล Treatment ที่เกี่ยวข้อง
        $treatment = Treatment::find($treatmentId);

        if (!$treatment) {
            return response()->json(['message' => 'ไม่พบข้อมูลการรักษา'], 404);
        }

        // ค้นหา soldier จาก medicalReport ที่เชื่อมโยงกับ Treatment
        $soldier = $treatment->checkin->appointment->medicalReport->soldier;

        if (!$soldier) {
            return response()->json(['message' => 'ไม่พบข้อมูลทหาร'], 404);
        }

        // ค้นหา medicalReport เก่าที่เชื่อมโยงกับ Treatment
        $oldMedicalReport = $treatment->checkin->appointment->medicalReport;

        if (!$oldMedicalReport) {
            Log::info('ไม่พบข้อมูล oldMedicalReport');
            return response()->json(['message' => 'ไม่พบข้อมูล medical_report เก่า'], 404);
        }

        Log::info('Old Medical Report ID: ' . $oldMedicalReport->id);

        // สร้าง Medical Report ใหม่สำหรับ Follow-up
        $newMedicalReport = MedicalReport::create([
            'soldier_id' => $soldier->id,
            'symptom_description' => 'นัดติดตามอาการ',
            'status' => 'approved',
            'report_date' => now(),
            'previous_report_id' => $oldMedicalReport->id,
        ]);

        Log::info('New Medical Report previous_report_id (after creation): ' . $newMedicalReport->previous_report_id);

        // สร้าง VitalSign ใหม่
        $newVitalSign = VitalSign::create([
            'temperature' => null,
            'blood_pressure' => null,
            'heart_rate' => null,
            'source' => 'appointment',
            'risk_level' => null,
        ]);

        // อัปเดต MedicalReport ให้เชื่อมกับ VitalSign
        $newMedicalReport->update(['vital_signs_id' => $newVitalSign->id]);

        // สร้าง Appointment สำหรับ follow-up
        $newAppointment = Appointment::create([
            'medical_report_id' => $newMedicalReport->id,
            'appointment_date' => $request->appointment_date,
            'appointment_location' => $request->appointment_location,
            'case_type' => $request->case_type,
            'status' => 'scheduled',
            'is_follow_up' => 1,
        ]);

        // เชื่อม Medical Report กับ Appointment
        $newMedicalReport->update(['appointment_id' => $newAppointment->id]);

        // สร้าง Checkin สำหรับนัดหมายใหม่นี้
        $checkin = Checkin::create([
            'appointment_id' => $newAppointment->id,
            'checkin_status' => 'not-checked-in',
            'checkin_time' => now(),
        ]);

        // ✅ หากนัดหมายเดิมเสร็จสิ้น อัปเดตสถานะ treatment เป็น treated
        $appointment = $treatment->checkin->appointment;

        if ($appointment && $appointment->status === 'completed') {
            $treatment->treatment_status = 'treated';
            $treatment->save();
            Log::info('เปลี่ยนสถานะ treatment เป็น treated สำเร็จ');
        }

        return response()->json([
            'message' => 'สร้างการนัดหมายใหม่และ Medical Report ใหม่สำหรับ Follow-up สำเร็จ',
            'appointment' => $newAppointment,
            'medical_report' => $newMedicalReport,
            'vital_sign' => $newVitalSign,
            'checkin' => $checkin
        ]);
    }

    public function addDiagnosis(Request $request)
    {
        $departmentType = $request->is('er/add-diagnosis*') ? 'er' : 'opd';
        Log::debug("Request Path: " . $request->path());  // บันทึก path ที่เข้ามา
        // ตรวจสอบข้อมูลที่รับเข้ามาจากฟอร์ม
        $request->validate([
            'treatment_id' => 'required|integer',
            'doctor_name' => 'required|string',
            'temperature' => 'nullable|numeric',
            'blood_pressure' => 'nullable|string',
            'heart_rate' => 'nullable|integer',
            'icd10_code' => 'required|string',
            'treatment_status' => 'required|in:Admit,Refer,Discharge,Follow-up',
            'notes' => 'nullable|string',
            'appointment_date' => 'nullable|date', // กรณี Follow-up
            'appointment_location' => 'nullable|string',
            'case_type' => 'nullable|in:normal,critical',
            'training_instruction' => 'nullable|string|max:255',
        ]);

        // ค้นหาข้อมูลโรคในฐานข้อมูล
        $diseases = ICD10Disease::whereIn('icd10_code', explode(',', $request->icd10_code))->get();
        if ($diseases->isEmpty()) {
            Log::warning('ไม่พบข้อมูลโรคสำหรับรหัสที่ระบุ: ' . $request->icd10_code);
            return response()->json(['message' => 'ไม่พบข้อมูลโรคสำหรับรหัสที่ระบุ'], 404);
        }
        // ตรวจสอบ department_type โดยใช้ request->is() เพื่อเช็คว่าเป็น ER หรือไม่

        // เพิ่มการตรวจสอบค่าของ department_type ที่กำหนด
        try {
            DB::beginTransaction();

            // ค้นหาข้อมูล treatment ที่มีอยู่แล้ว
            $treatment = Treatment::find($request->treatment_id);

            // ตรวจสอบว่า treatment ถูกต้องหรือไม่
            if (!$treatment) {
                Log::error('ไม่พบข้อมูล Treatment สำหรับ treatment_id: ' . $request->treatment_id);
                throw new \Exception("ไม่พบข้อมูล Treatment สำหรับ treatment_id: {$request->treatment_id}");
            }

            // ค้นหา VitalSign จาก Treatment
            $vitalSign = $treatment->checkin->appointment->medicalReport->vitalSign;

            // ตรวจสอบว่า VitalSign มีอยู่
            if (!$vitalSign) {
                Log::error('ไม่พบข้อมูล Vital Signs สำหรับ treatment_id: ' . $request->treatment_id);
                throw new \Exception("ไม่พบข้อมูล Vital Signs สำหรับ treatment_id: {$request->treatment_id}");
            }

            // ค้นหา Checkin จาก Treatment
            $checkin = $treatment->checkin;

            // ตรวจสอบว่า Checkin ถูกต้องหรือไม่
            if (!$checkin) {
                Log::error('ไม่พบข้อมูล Checkin สำหรับ treatment_id: ' . $request->treatment_id);
                throw new \Exception("ไม่พบข้อมูล Checkin สำหรับ treatment_id: {$request->treatment_id}");
            }

            // ค้นหา Appointment จาก Checkin
            $appointment = $checkin->appointment;

            // ตรวจสอบว่า Appointment ถูกต้องหรือไม่
            if (!$appointment) {
                Log::error('ไม่พบข้อมูล Appointment สำหรับ treatment_id: ' . $request->treatment_id);
                throw new \Exception("ไม่พบข้อมูล Appointment สำหรับ treatment_id: {$request->treatment_id}");
            }

            // ค้นหา MedicalReport จาก Appointment
            $medicalReport = $appointment->medicalReport;

            // ตรวจสอบว่า MedicalReport ถูกต้องหรือไม่
            if (!$medicalReport) {
                Log::error('ไม่พบข้อมูล Medical Report สำหรับ soldier_id: ' . $appointment->soldier_id);
                throw new \Exception("ไม่พบข้อมูล Medical Report สำหรับ soldier_id: {$appointment->soldier_id}");
            }

            // สร้างข้อมูลการวินิจฉัยในตาราง medical_diagnosis
            $diagnosis = MedicalDiagnosis::create([
                'treatment_id' => $request->treatment_id,
                'doctor_name' => $request->doctor_name,
                'treatment_status' => $request->treatment_status,
                'department_type' => $departmentType,
                'vital_signs_id' => $vitalSign->id,
                'diagnosis_date' => now(),
                'notes' => $request->notes,
                'training_instruction' => $request->input('training_instruction'),
            ]);

            // เพิ่มข้อมูลในตาราง medical_diagnosis_diseases
            foreach ($diseases as $disease) {
                $diagnosis->diseases()->attach($disease->id);
            }
            // เพิ่มข้อมูลการวินิจฉัยในตาราง medical_diagnosis สำหรับกรณี "Admit"
            if ($request->treatment_status === 'Admit') {
                $admitDiagnosis = MedicalDiagnosis::create([
                    'treatment_id' => $request->treatment_id,
                    'doctor_name' => $request->doctor_name,
                    'treatment_status' => 'Admit',
                    'department_type' => 'ipd',
                    'vital_signs_id' => $vitalSign->id,
                    'diagnosis_date' => now(), // เพื่อไม่ให้เวลาชนกัน
                    'notes' => $request->notes,
                ]);

                // เพิ่มข้อมูลในตาราง medical_diagnosis_diseases สำหรับการวินิจฉัย "Admit"
                foreach ($diseases as $disease) {
                    $admitDiagnosis->diseases()->attach($disease->id);
                }
            }
            if ($request->treatment_status === 'Follow-up' && $request->appointment_date) {
                // สร้าง Medical Report และ Appointment ใหม่สำหรับ Follow-up

                return $this->createFollowUpMedicalReportAndAppointment($request->treatment_id, $request);
            }

            // ค้นหา checkin ที่เชื่อมโยงกับ treatment โดยใช้ appointment_id
            $checkin = Checkin::where('appointment_id', $diagnosis->treatment->checkin->appointment_id)->first();

            if (
                $checkin && $checkin->checkin_status === 'checked-in' &&
                in_array($request->treatment_status, ['Admit', 'Refer', 'Discharge', 'Follow-up'])
            ) {
                // ใช้ appointment_id จาก checkin เพื่อค้นหา appointment
                $appointment = Appointment::find($checkin->appointment_id);
                if ($appointment) {
                    // เปลี่ยนสถานะของการนัดหมายเป็น 'completed'
                    $appointment->status = 'completed';
                    $appointment->save();
                }
            }


            DB::commit();
            return response()->json(['message' => 'บันทึกข้อมูลการวินิจฉัยเรียบร้อยแล้ว'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('เกิดข้อผิดพลาดใน addDiagnosis: ' . $e->getMessage());
            return response()->json(['message' => 'เกิดข้อผิดพลาด', 'error' => $e->getMessage()], 500);
        }
    }
    public function getDiseaseInfoByCodes($codes)
    {
        if (empty($codes)) {
            return response()->json(['message' => 'กรุณาระบุรหัสโรค'], 400);
        }

        $codeArray = explode(',', $codes);

        $diseases = ICD10Disease::whereIn('icd10_code', $codeArray)->get();

        if ($diseases->isEmpty()) {
            return response()->json(['message' => 'ไม่พบข้อมูลโรค'], 404);
        }

        $diseaseDescriptions = $diseases->map(function ($disease) {
            return [
                'icd10_code' => $disease->icd10_code,
                'disease_name' => $disease->disease_name_en,
            ];
        });

        return response()->json(['diseases' => $diseaseDescriptions]);
    }

}

