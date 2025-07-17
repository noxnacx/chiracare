<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        Log::info('๐•’ Laravel schedule() called at ' . now('Asia/Bangkok'));

        $schedule->command('appointments:mark-missed')
            ->everyMinute()
            ->timezone('Asia/Bangkok');
    }

    // ❗ สำคัญมาก ต้องมีแบบนี้เท่านั้น
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands'); // ✅ อันนี้ขาดไม่ได้เด็ดขาด
        require base_path('routes/console.php');
    }
}
