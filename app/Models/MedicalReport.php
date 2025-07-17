<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalReport extends Model
{
    use HasFactory;

    protected $table = 'medical_report'; // ชื่อตารางในฐานข้อมูล

    protected $fillable = [
        'soldier_id',
        'symptom_description',
        'pain_score',
        'vital_signs_id',
        'status', // เพิ่มฟิลด์ status
        'report_date', // เพิ่มฟิลด์ report_date
    ];

    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }



    public function vitalSign()
    {
        return $this->belongsTo(VitalSign::class, 'vital_signs_id');
    }

    public function images()
    {
        return $this->hasMany(MedicalReportImage::class, 'medical_report_id');
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatment_id');
    }

    // เชื่อมโยงกับ Checkin ผ่าน checkin_id (ถ้ามีความสัมพันธ์)
    public function checkin()
    {
        return $this->belongsTo(Checkin::class, 'checkin_id');
    }

    // เชื่อมโยงกับ Appointment ผ่าน checkin->appointment_id
    // ใน MedicalReport.php
    // ใน MedicalReport.php
    public function appointment()
    {
        return $this->hasOne(Appointment::class, 'medical_report_id');
    }




    public function medicalDiagnoses()
    {
        return $this->hasMany(MedicalDiagnosis::class, 'medical_report_id');
    }

    // เชื่อมโยงกับ MedicalReport ผ่าน Appointment โดยใช้ hasOneThrough




    // เพิ่ม timestamp เพื่อให้ Laravel อัปเดต created_at และ updated_at อัตโนมัติ
    public $timestamps = true;
}
