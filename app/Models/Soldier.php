<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soldier extends Model
{
    use HasFactory;

    protected $table = 'soldier'; // กำหนดชื่อตาราง

    protected $primaryKey = 'id'; // Primary Key

    public $timestamps = true; // ใช้งาน created_at, updated_at อัตโนมัติ


    protected $fillable = [
        'soldier_id_card',
        'first_name',
        'last_name',
        'rotation_id',
        'training_unit_id',
        'affiliated_unit',
        'weight_kg',
        'height_cm',
        'medical_allergy_food_history',
        'underlying_diseases',
        'selection_method',
        'service_duration',
        'consent_accepted',
        'initial_assessment_complete',
        'soldier_image'
    ];

    // **ความสัมพันธ์กับ Rotation (เหมือนเดิม)**
    public function rotation()
    {
        return $this->belongsTo(Rotation::class, 'rotation_id'); //
    }

    // **ความสัมพันธ์กับ TrainingUnit (เหมือนเดิม)**
    public function trainingUnit()
    {
        return $this->belongsTo(TrainingUnit::class, 'training_unit_id'); //
    }

    // ⭐️ เพิ่ม: ความสัมพันธ์ไปยังผลการประเมินทั้งหมด
    // ทำให้สามารถเรียก $soldier->assessmentScores เพื่อดูประวัติการทำแบบประเมินทั้งหมดได้
    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class, 'soldier_id')->orderBy('assessment_date', 'desc');
    }

    // ⭐️ เพิ่ม: ความสัมพันธ์ไปยังประวัติการรักษา
    public function medicalReports()
    {
        return $this->hasMany(MedicalReport::class, 'soldier_id'); //
    }

    // **Mutator สำหรับการเก็บรูปภาพ (URL)**
    public function getSoldierImageAttribute($value)
    {
        return asset('storage/soldiers/' . $value);
    }

    public function medicalDiagnosis()
    {
        // หากความสัมพันธ์นี้เป็นแบบหนึ่งต่อหลาย ให้ใช้ hasMany
        return $this->hasMany(MedicalDiagnosis::class);
    }




    // Soldier.php

    // ใน Soldier.php
    public function medicalDiagnoses()
    {
        return $this->hasManyThrough(
            MedicalDiagnosis::class,  // โมเดลที่เชื่อมโยง
            MedicalReport::class,     // โมเดลที่เป็นจุดเริ่มต้น
            'soldier_id',             // คีย์จาก Soldier ไปที่ MedicalReport
            'medical_report_id',      // คีย์จาก MedicalReport ไปที่ MedicalDiagnosis
            'id',                     // คีย์หลักจาก Soldier
            'id'                      // คีย์หลักจาก MedicalReport
        );
    }


    // ใน Model MedicalDiagnosis.php
    public function diseases()
    {
        return $this->belongsToMany(Icd10Disease::class, 'medical_diagnosis_diseases', 'medical_diagnosis_id', 'icd10_disease_id');
    }
    // ใน Soldier.php
    public function appointment()
    {
        return $this->hasOne(Appointment::class);
    }

    public function mentalHealthTracking()
    {
        return $this->hasMany(AssessmentStatusTracking::class, 'soldier_id');
    }

}
