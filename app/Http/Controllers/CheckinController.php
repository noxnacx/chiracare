<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Checkin;
use App\Models\Soldier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckinController extends Controller
{
    public function checkInByIDCard(Request $request)
    {
        $validated = $request->validate([
            'soldier_id_card' => 'required|string|size:13',
        ]);

        $idCard = $request->soldier_id_card;
        $today = Carbon::today()->toDateString();

        // 🔍 ค้นหาทหารจากเลขบัตรประชาชน
        $soldier = Soldier::where('soldier_id_card', $idCard)->first();
        if (!$soldier) {
            return response()->json(['status' => 'error', 'message' => 'ไม่พบบัตรประชาชนนี้ในระบบ'], 404);
        }

        // 🔍 ค้นหานัดหมายของวันนี้
        $appointment = Appointment::whereHas('medicalReport', function ($query) use ($soldier) {
            $query->where('soldier_id', $soldier->id);
        })
            ->whereDate('appointment_date', $today)
            ->first();

        if (!$appointment) {
            return response()->json(['status' => 'error', 'message' => 'ยังไม่ถึงวันนัดหมาย ไม่สามารถเช็คอินได้'], 403);
        }

        // 🔍 ค้นหา Check-in
        $checkin = Checkin::where('appointment_id', $appointment->id)->first();
        if (!$checkin) {
            return response()->json(['status' => 'error', 'message' => 'ไม่พบข้อมูลเช็คอิน'], 404);
        }

        // ❌ ป้องกันการเช็คอินซ้ำ
        if ($checkin->checkin_status === 'checked-in') {
            return response()->json(['status' => 'error', 'message' => 'เช็คอินไปแล้ว'], 400);
        }

        // ✅ อัปเดตสถานะเช็คอิน
        $checkin->update([
            'checkin_time' => now(),
            'checkin_status' => 'checked-in',
        ]);

        return response()->json(['status' => 'success', 'message' => 'เช็คอินสำเร็จ']);
    }




    public function viewCheckin()
    {
        $today = Carbon::today()->toDateString();

        // ดึงข้อมูลทหารที่มีนัดหมายวันนี้ พร้อมข้อมูลการเช็คอิน
        $appointments = Appointment::with(['medicalReport.soldier', 'checkin'])
            ->whereDate('appointment_date', $today)
            ->get();

        return view('checkin.checkin', compact('appointments'));
    }
}
