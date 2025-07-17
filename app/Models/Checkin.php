<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory;

    protected $table = 'checkin';
    public $timestamps = false; // ✅ ปิดการใช้งาน timestamps (updated_at & created_at)


    protected $fillable = [
        'appointment_id',
        'checkin_time',
        'checkin_status',
    ];

    // ใน Checkin.php
    


    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function treatment()
    {
        return $this->hasOne(Treatment::class, 'checkin_id');
    }
}
