<?php

namespace App\Http\Controllers;

use App\Models\Checkin;
use App\Models\Treatment;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MedicalReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingUnit;



class AppointmentController extends Controller
{
    // แสดงฟอร์มสร้างนัดหมาย


    public function showCreateAppointmentForm($medicalReportId)
    {
        $medicalReport = MedicalReport::findOrFail($medicalReportId);
        return view('admin-hospital.create_appointment', compact('medicalReport'));
    }

    // บันทึกข้อมูลนัดหมายลงฐานข้อมูล
    public function listPendingAppointments()
    {
        // ดึงเฉพาะ medical_report ที่มีสถานะเป็น 'sent'
        $medicalReports = MedicalReport::where('status', 'sent')->get();

        return view('admin-hospital.appointments', compact('medicalReports'));
    }


    public function scheduleAppointments(Request $request)
    {
        $request->validate([
            'medical_report_ids' => 'required|array',
            'medical_report_ids.*' => 'exists:medical_report,id',
            'appointment_date' => 'required|date',
            'appointment_location' => 'required|string',
            'case_type' => 'required|in:normal,critical'
        ]);

        DB::beginTransaction(); // 🔹 ใช้ Transaction เพื่อให้แน่ใจว่าข้อมูลถูกบันทึกครบ

        try {
            foreach ($request->medical_report_ids as $id) {
                // ✅ สร้าง Appointment
                $appointment = Appointment::create([
                    'medical_report_id' => $id,
                    'appointment_date' => $request->appointment_date,
                    'appointment_location' => $request->appointment_location,
                    'case_type' => $request->case_type,
                    'status' => 'scheduled'
                ]);

                // ✅ เปลี่ยนสถานะ medical_report เป็น approved
                MedicalReport::where('id', $id)->update(['status' => 'approved']);

                // ✅ สร้าง Check-in
                $checkin = Checkin::create([
                    'appointment_id' => $appointment->id,
                    'checkin_time' => null,
                    'checkin_status' => 'not-checked-in',
                ]);

                Log::info("✅ Check-in ID ที่สร้าง: " . $checkin->id); // 🔹 Log ค่าก่อนสร้าง Treatment

                // ✅ สร้าง Treatment
                $treatment = Treatment::create([
                    'checkin_id' => $checkin->id,
                    'treatment_date' => null,
                    'treatment_status' => 'not-treated',
                ]);

                Log::info("✅ Treatment ID ที่สร้าง: " . $treatment->id); // 🔹 Log ค่าหลังจากสร้าง Treatment
            }

            DB::commit(); // 🔹 บันทึกข้อมูลทั้งหมดถ้าไม่มี Error
            return response()->json(['status' => 'success', 'message' => 'นัดหมายสำเร็จ พร้อมสร้าง Check-in และ Treatment']);
        } catch (\Exception $e) {
            DB::rollBack(); // 🔹 ยกเลิกการบันทึกหากเกิด Error
            Log::error("❌ เกิดข้อผิดพลาดในการบันทึก Treatment: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด', 'error' => $e->getMessage()], 500);
        }
    }

    public function loadAppointmentForEdit($id)
    {
        $appointment = Appointment::findOrFail($id);

        return response()->json([
            'id' => $appointment->id,
            'appointment_date' => \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d\TH:i'),
            'appointment_location' => $appointment->appointment_location,
            'case_type' => $appointment->case_type,
        ]);
    }

    public function updateAppointmentDetails(Request $request, $id)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_location' => 'required|string',
            'case_type' => 'required|in:normal,critical'
        ]);

        $appointment = Appointment::findOrFail($id);

        // ✅ อัปเดตข้อมูล appointment
        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_location' => $request->appointment_location,
            'case_type' => $request->case_type,
            'status' => 'scheduled'
        ]);

        // ✅ ตรวจสอบว่ามี checkin ที่เกี่ยวข้องหรือยัง
        $checkin = $appointment->checkin;
        if ($checkin) {
            // อัปเดต checkin ที่มีอยู่
            $checkin->update(['checkin_status' => 'not-checked-in']);
        } else {
            // ถ้ายังไม่มี ให้สร้างใหม่
            $appointment->checkin()->create([
                'checkin_status' => 'not-checked-in',
                'checkin_time' => now()
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'บันทึกการแก้ไขสำเร็จ']);
    }




    public function listApprovedAppointments(Request $request)
    {
        // Get the selected status from the request, defaulting to 'rescheduled'
        $selectedStatus = $request->input('status', 'rescheduled');

        // Retrieve appointments based on selected status
        $appointments = Appointment::with(['medicalReport.soldier', 'medicalReport.vitalSign'])
            ->where('status', $selectedStatus)  // Filter by the selected status (rescheduled)
            ->get();

        // Pass appointments and selectedStatus to the view
        return view('admin-hospital.success_appointment', compact('appointments', 'selectedStatus'));
    }




    public function listMissedAppointments()
    {
        // ดึงรายการที่สถานะเป็น 'missed'
        $missedAppointments = Appointment::where('status', 'missed')
            ->with(['medicalReport.soldier', 'medicalReport.vitalSign'])
            ->get();

        return view('admin-hospital.miss_appointment', compact('missedAppointments'));
    }

    public function updateMissedAppointments(Request $request)
    {
        $appointmentIds = $request->input('medical_report_ids');

        if (!is_array($appointmentIds) || empty($appointmentIds)) {
            return response()->json(['status' => 'error', 'message' => 'กรุณาเลือกการนัดหมายที่ต้องการอัปเดต'], 400);
        }

        // รับข้อมูลจากฟอร์ม
        $appointmentDate = $request->input('appointment_date');
        $appointmentLocation = $request->input('appointment_location');
        $caseType = $request->input('case_type');

        try {
            // อัปเดตสถานะการนัดหมายจาก 'missed' เป็น 'scheduled'
            Appointment::whereIn('id', $appointmentIds)
                ->where('status', 'missed')
                ->update([
                    'status' => 'scheduled',
                    'appointment_date' => $appointmentDate,
                    'appointment_location' => $appointmentLocation,
                    'case_type' => $caseType
                ]); // เปลี่ยนสถานะจาก 'missed' เป็น 'scheduled' และอัปเดตข้อมูลอื่นๆ

            return response()->json(['status' => 'success', 'message' => 'อัปเดตสถานะการนัดหมายสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัปเดต', 'error' => $e->getMessage()], 500);
        }
    }


    public function listScheduledByUnit(Request $request)
    {
        // รับวันที่จากคำขอ (ถ้าไม่มีก็ให้แสดงข้อมูลทั้งหมด)
        $selectedDate = $request->input('date', '');
        $selectedStatus = $request->input('status', '');  // ค่าเริ่มต้นเป็นค่าว่าง

        // ดึงข้อมูลนัดหมาย โดยกรองตามสถานะ 'scheduled' หรือ 'rescheduled'
        $appointments = Appointment::with(['medicalReport.soldier', 'medicalReport.soldier.trainingUnit'])
            ->whereIn('status', ['scheduled', 'rescheduled'])  // กรองสถานะเป็น 'scheduled' หรือ 'rescheduled'
            ->when($selectedDate, function ($query) use ($selectedDate) {
                return $query->whereDate('appointment_date', $selectedDate);
            })
            ->when($selectedStatus, function ($query) use ($selectedStatus) {
                return $query->where('status', $selectedStatus);
            })
            ->get();

        return view('trainingUnit.scheduled_by_unit', compact('appointments', 'selectedDate', 'selectedStatus'));
    }



    // ใน Controller:
    public function rescheduleStatus(Request $request)
    {
        $appointmentIds = $request->input('appointment_ids');
        $appointmentDate = $request->input('appointment_date');
        $appointmentLocation = $request->input('appointment_location');
        $caseType = $request->input('case_type');

        // Ensure that the appointmentIds are not empty
        if (empty($appointmentIds)) {
            return response()->json(['status' => 'error', 'message' => 'Appointment IDs are required']);
        }

        // Update the appointment status to "rescheduled"
        Appointment::whereIn('id', $appointmentIds)->update([
            'appointment_date' => $appointmentDate,
            'appointment_location' => $appointmentLocation,
            'case_type' => $caseType,
            'status' => 'rescheduled' // Update the status to "rescheduled"
        ]);

        return response()->json(['status' => 'success', 'message' => 'Appointments have been rescheduled']);
    }

    public function rescheduleToScheduled(Request $request)
    {
        // รับข้อมูลจาก request
        $appointmentIds = $request->input('appointment_ids');
        $appointmentDate = $request->input('appointment_date');
        $appointmentLocation = $request->input('appointment_location');
        $caseType = $request->input('case_type');

        // อัปเดตการนัดหมาย
        Appointment::whereIn('id', $appointmentIds)->update([
            'appointment_date' => $appointmentDate,
            'appointment_location' => $appointmentLocation,
            'case_type' => $caseType,
            'status' => 'scheduled' // อัปเดตสถานะเป็น Scheduled
        ]);

        return response()->json(['status' => 'success', 'message' => 'การนัดหมายได้รับการอัปเดตแล้ว']);
    }



    // Method สำหรับการอัปเดตการนัดหมาย
    public function rescheduleAppointment(Request $request)
    {
        try {
            $request->validate([
                'appointment_ids' => 'required|array',
                'appointment_date' => 'required|date',
                'appointment_location' => 'required|string',
                'case_type' => 'required|string',
            ]);

            $appointmentIds = $request->input('appointment_ids');
            $appointmentDate = $request->input('appointment_date');
            $appointmentLocation = $request->input('appointment_location');
            $caseType = $request->input('case_type');

            Appointment::whereIn('id', $appointmentIds)->update([
                'appointment_date' => $appointmentDate,
                'appointment_location' => $appointmentLocation,
                'case_type' => $caseType,
                'status' => 'scheduled' // อัปเดตสถานะเป็น Scheduled
            ]);

            return response()->json(['status' => 'success', 'message' => 'การนัดหมายได้รับการอัปเดตแล้ว']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }




    // ในคอนโทรลเลอร์ของคุณ






}
