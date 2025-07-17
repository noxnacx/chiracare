<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
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
    // ✅ วางโค้ดทั้งหมดเริ่มจากตรงนี้

    \Illuminate\Support\Carbon::macro('thaidate', function ($format) {
        $date = $this; // $this ในที่นี้คือ object ของ Carbon
        $thaiMonths = [
            'January'   => 'มกราคม', 'February'  => 'กุมภาพันธ์', 'March'     => 'มีนาคม',
            'April'     => 'เมษายน', 'May'       => 'พฤษภาคม', 'June'      => 'มิถุนายน',
            'July'      => 'กรกฎาคม', 'August'    => 'สิงหาคม', 'September' => 'กันยายน',
            'October'   => 'ตุลาคม', 'November'  => 'พฤศจิกายน', 'December'  => 'ธันวาคม'
        ];
        $thaiDayOfWeek = [
            'Sunday'    => 'วันอาทิตย์', 'Monday'    => 'วันจันทร์', 'Tuesday'   => 'วันอังคาร',
            'Wednesday' => 'วันพุธ', 'Thursday'  => 'วันพฤหัสบดี', 'Friday'    => 'วันศุกร์',
            'Saturday'  => 'วันเสาร์'
        ];

        // แปลง ค.ศ. เป็น พ.ศ. (บวก 543)
        $buddhistYear = $date->year + 543;

        // แทนที่ปี ค.ศ. ด้วย พ.ศ.
        $formattedDate = str_replace((string)$date->year, (string)$buddhistYear, $date->format($format));

        // แทนที่เดือนและวันภาษาอังกฤษเป็นภาษาไทย
        $formattedDate = str_replace(array_keys($thaiMonths), array_values($thaiMonths), $formattedDate);
        $formattedDate = str_replace(array_keys($thaiDayOfWeek), array_values($thaiDayOfWeek), $formattedDate);

        return $formattedDate;
    });

    // สิ้นสุดโค้ดที่ต้องวาง
}
}
