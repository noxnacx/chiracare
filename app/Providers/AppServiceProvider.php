<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon; // ✅ 1. เพิ่มบรรทัดนี้
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        // ✅ 2. เพิ่มโค้ดส่วนนี้ทั้งหมดเข้าไป
        Carbon::macro('thaidate', function ($format) {
            $date = $this; // $this ในที่นี้คือ object ของ Carbon
            $thaiMonths = [
                'January'   => 'มกราคม', 'February'  => 'กุมภาพันธ์', 'March'     => 'มีนาคม',
                'April'     => 'เมษายน', 'May'       => 'พฤษภาคม', 'June'      => 'มิถุนายน',
                'July'      => 'กรกฎาคม', 'August'    => 'สิงหาคม', 'September' => 'กันยายน',
                'October'   => 'ตุลาคม', 'November'  => 'พฤศจิกายน', 'December'  => 'ธันวาคม'
            ];

            // แปลงเดือนภาษาอังกฤษเป็นไทย
            $thaiFormattedDate = str_replace(
                array_keys($thaiMonths),
                array_values($thaiMonths),
                $date->format($format)
            );

            // แปลงปี ค.ศ. เป็น พ.ศ.
            return str_replace(
                $date->format('Y'),
                $date->year + 543,
                $thaiFormattedDate
            );
        });
    }
}
