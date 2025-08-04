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

        // ตรวจสอบว่าอยู่ในช่วง 04:00 ถึง 05:59
        $start = $now->copy()->setTime(00, 0, 0);
        $end = $now->copy()->setTime(00, 41, 0);

        if (!$now->between($start, $end)) {
            Log::info("⏩ ข้ามการตรวจสอบ (ไม่อยู่ในช่วงเวลา 04:00–05:59)");
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

        $processedCount = 0;

        foreach ($appointments as $appointment) {
            // เก็บวันที่นัดเดิมไว้ก่อนเปลี่ยนสถานะ
            $originalAppointmentDate = $appointment->appointment_date;

            // อัปเดต appointment
            $appointment->status = 'missed';
            $appointment->was_missed = 1;  // ← เพิ่มอันนี้
            $appointment->missed_appointment_date = $originalAppointmentDate;  // ← เพิ่มอันนี้
            $appointment->save();

            // อัปเดต checkin
            $appointment->checkin->checkin_status = 'missed';
            $appointment->checkin->save();

            $processedCount++;

            Log::info("🔴 Marked missed: Appointment ID {$appointment->id}, Checkin ID {$appointment->checkin->id}", [
                'original_date' => $originalAppointmentDate,
                'was_missed' => 1,
                'missed_appointment_date' => $originalAppointmentDate
            ]);
        }

        Log::info("✅ จบการตรวจสอบ missed ทั้งหมด {$processedCount} รายการ เวลา " . $now->format('H:i:s'));
    }
}
