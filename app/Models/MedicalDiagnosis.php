<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MedicalDiagnosis extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'medical_diagnosis';
    protected $fillable = [
        'treatment_id',
        'doctor_name',
        'treatment_status',
        'diagnosis_date',
        'department_type', // ต้องมีค่านี้
        'medical_report_id',
        'vital_signs_id',
    ];

    // เชื่อมโยงกับ Treatment


    // เชื่อมโยงกับ Checkin ผ่าน Treatment
    public function checkin()
    {
        return $this->treatment->checkin; // ใช้ความสัมพันธ์ระหว่าง Treatment และ Checkin
    }

    // เชื่อมโยงกับ Appointment ผ่าน Checkin
    public function appointment()
    {
        return $this->checkin->appointment; // ใช้ความสัมพันธ์ระหว่าง Checkin และ Appointment
    }

    // เชื่อมโยงกับ MedicalReport ผ่าน Appointment


    // ความสัมพันธ์กับ ICD10Disease (หลายต่อหลาย)
    public function diseases()
    {
        return $this->belongsToMany(ICD10Disease::class, 'medical_diagnosis_diseases', 'medical_diagnosis_id', 'icd10_disease_id');
    }

    // ความสัมพันธ์กับ VitalSign


    // ใน MedicalDiagnosis.php


    // ลบฟังก์ชันที่ใช้ hasOneThrough




    public function medicalReport()
    {
        return $this->belongsTo(MedicalReport::class, 'medical_report_id');
    }

    // ความสัมพันธ์กับ Treatment
    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatment_id');
    }

    // ความสัมพันธ์กับ VitalSign
    public function vitalSigns()
    {
        return $this->belongsTo(VitalSign::class, 'vital_signs_id');
    }


}
