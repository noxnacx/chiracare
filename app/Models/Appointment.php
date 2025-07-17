<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointment';
    protected $fillable = [
        'medical_report_id',
        'appointment_date',
        'appointment_location',
        'case_type',
        'status',
        'is_follow_up',
    ];
    // กำหนดค่า default หากไม่ถูกส่งเข้ามา
    protected $attributes = [
        'is_follow_up' => 0,  // ถ้าไม่กำหนดจะเป็น 0
    ];

    // หรือถ้าใช้ casts
    protected $casts = [
        'is_follow_up' => 'boolean', // กำหนดเป็น boolean
    ];
    public function medicalReport()
    {
        return $this->belongsTo(MedicalReport::class, 'medical_report_id');
    }


    public function checkin()
    {
        return $this->hasOne(Checkin::class, 'appointment_id');
    }


}

