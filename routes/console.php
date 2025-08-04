<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // ✅ ต้อง import ด้วย!

// Artisan command ตัวอย่าง
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ✅ เพิ่ม schedule ของคุณไว้ตรงนี้ได้เลย
Schedule::command('appointments:mark-missed')
    ->everyMinute()
    ->timezone('Asia/Bangkok');
