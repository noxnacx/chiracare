<?php

namespace App\Http\Controllers;
use App\Models\Rotation;
use App\Models\TrainingUnit;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MedicalReport;
use Barryvdh\DomPDF\Facade\Pdf;

class HospitalAppointmentController extends Controller
{
    // แสดงรายการที่มีสถานะ 'sent'
    public function sentAppointments(Request $request)
    {
        // รับค่าจาก query string
        $status = $request->input('status', 'sent');
        $date = $request->input('date');
        $caseType = $request->input('case_type');
        $rotationId = $request->input('rotation_id');
        $trainingUnitId = $request->input('training_unit_id');

        // เริ่ม query หลักจาก MedicalReport
        $query = MedicalReport::query();

        // 🔎 Filter ตามสถานะ
        if ($status === 'sent') {
            $query->where('status', 'sent');
        }

        if (in_array($status, ['scheduled', 'missed'])) {
            $query->whereHas('appointment', function ($q) use ($status) {
                $q->where('status', $status);
            });

            // ✅ กรองตามวันที่ (appointment_date)
            if ($date) {
                $query->whereHas('appointment', function ($q) use ($date) {
                    $q->whereDate('appointment_date', $date);
                });
            }

            // ✅ กรองตามประเภทเคส (normal/critical)
            if ($caseType && $caseType !== 'all') {
                $query->whereHas('appointment', function ($q) use ($caseType) {
                    $q->where('case_type', $caseType);
                });
            }
        }

        // 🔎 กรองตามผลัด (rotation)
        if ($rotationId) {
            $query->whereHas('soldier.rotation', function ($q) use ($rotationId) {
                $q->where('id', $rotationId);
            });
        }

        // 🔎 กรองตามหน่วยฝึก
        if ($trainingUnitId) {
            $query->whereHas('soldier', function ($q) use ($trainingUnitId) {
                $q->where('training_unit_id', $trainingUnitId);
            });
        }

        // โหลดข้อมูลความสัมพันธ์ที่จำเป็น
        $medicalReports = $query->with([
            'soldier',
            'soldier.trainingUnit',
            'soldier.rotation',
            'appointment'
        ])->get();

        // ดึงข้อมูล dropdown
        $rotations = Rotation::all();
        $trainingUnits = TrainingUnit::all();

        // ส่งไปที่ view
        return view('admin-hospital.approved_appointment', compact(
            'medicalReports',
            'rotations',
            'trainingUnits'
        ));
    }


    // อัปเดตสถานะเป็น 'approved' และสร้างนัดหมาย
    public function approveAppointment(Request $request)
    {
        // ตรวจสอบค่าที่ส่งมา
        $request->validate([
            'ids' => 'required|array',
            'appointment_date' => 'required|date',
            'appointment_location' => 'required|string',
            'case_type' => 'required|in:normal,critical',
        ]);

        foreach ($request->ids as $id) {
            // อัปเดตสถานะเป็น 'approved'
            MedicalReport::where('id', $id)->update(['status' => 'approved']);

            // บันทึกข้อมูลการนัดหมาย
            Appointment::create([
                'medical_report_id' => $id,
                'appointment_date' => $request->appointment_date,
                'appointment_location' => $request->appointment_location,
                'case_type' => $request->case_type,
                'status' => 'approved', // หลังจากนัดหมายเสร็จ สถานะเป็น 'scheduled'
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'นัดหมายสำเร็จ']);
    }


    public function scheduleAppointments(Request $request)
    {
        // ✅ Debug เช็คค่าที่ถูกส่งจาก Frontend
        dd($request->all());

        $request->validate([
            'medical_report_ids' => 'required|array',
            'medical_report_ids.*' => 'exists:medical_reports,id',
            'appointment_date' => 'required|date',
            'appointment_location' => 'required|string',
            'case_type' => 'required|in:normal,critical'
        ]);

        foreach ($request->medical_report_ids as $id) {
            Appointment::create([
                'medical_report_id' => $id,
                'appointment_date' => $request->appointment_date,
                'appointment_location' => $request->appointment_location,
                'case_type' => $request->case_type,
                'status' => 'scheduled'
            ]);

            // อัปเดตสถานะของ medical report เป็น 'approved'
            MedicalReport::where('id', $id)->update(['status' => 'approved']);
        }

        return response()->json(['status' => 'success', 'message' => 'นัดหมายสำเร็จ']);
    }









    public function download($id)
    {
        dd(config('dompdf.fonts'));

        $appointment = Appointment::with(['medicalReport.soldier', 'medicalReport.soldier.rotation', 'medicalReport.soldier.trainingUnit'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('pdf.appointment', compact('appointment'));

        // ตั้งค่าฟอนต์เพิ่มเติม
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('defaultFont', 'Sarabun');
        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->download("appointment-{$id}.pdf");
    }

    public function downloadAll(Request $request)
    {


        $query = Appointment::with(['medicalReport.soldier', 'medicalReport.soldier.rotation', 'medicalReport.soldier.trainingUnit']);

        if ($request->status === 'scheduled') {
            $query->where('status', 'scheduled');
        }

        if ($request->filled('case_type') && $request->case_type !== 'all') {
            $query->where('case_type', $request->case_type);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->get();

        $pdf = Pdf::loadView('pdf.all_appointments', compact('appointments'));

        // ตั้งค่าฟอนต์เพิ่มเติม
        $pdf->setPaper('a4', 'portrait');
        $pdf = Pdf::loadView('pdf.all_appointments', compact('appointments'))
            ->setOption('defaultFont', 'Sarabun');

        $pdf->setOption('isRemoteEnabled', true);

        return $pdf->download("appointments-report-" . now()->format('YmdHis') . ".pdf");
    }

}
















