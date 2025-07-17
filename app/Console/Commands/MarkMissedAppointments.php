<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Appointment;
use Carbon\Carbon;

class MarkMissedAppointments extends Command
{
    protected $signature = 'appointments:mark-missed';
    protected $description = 'Mark appointments as missed if not checked in by deadline time (between 17:00 - 17:10)';

    public function handle()
    {
        Log::info('📌 COMMAND เริ่มทำงาน');

        $now = Carbon::now('Asia/Bangkok');
        Log::info("⏰ เวลาปัจจุบัน: {$now->format('Y-m-d H:i:s')}");

        // ตรวจสอบว่าอยู่ในช่วง 17:00 ถึง 17:10
        $start = $now->copy()->setTime(17, 0, 0);
        $end = $now->copy()->setTime(22, 40, 0);

        if (!$now->between($start, $end)) {
            Log::info("⏩ ข้ามการตรวจสอบ (ไม่อยู่ในช่วงเวลา 17:00–17:10)");
            return;
        }

        // ดึงรายการนัดหมายที่ยังไม่ได้เช็คอิน และถึงเวลานัดแล้ว
        $appointments = Appointment::where('appointment_date', '<=', $now)
            ->where('status', 'scheduled')
            ->whereHas('checkin', function ($query) {
                $query->where('checkin_status', 'not-checked-in');
            })
            ->with('checkin')
            ->get();

        if ($appointments->isEmpty()) {
            Log::info("✅ ไม่มีรายการที่ต้อง mark missed ณ ตอนนี้");
            return;
        }

        foreach ($appointments as $appointment) {
            $appointment->status = 'missed';
            $appointment->save();

            $appointment->checkin->checkin_status = 'missed';
            $appointment->checkin->save();

            Log::info("🔴 Marked missed: Appointment ID {$appointment->id}, Checkin ID {$appointment->checkin->id}");
        }

        Log::info("✅ จบการตรวจสอบ missed ทั้งหมด เวลา " . $now->format('H:i:s'));
    }
}
