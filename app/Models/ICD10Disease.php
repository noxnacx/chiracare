<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICD10Disease extends Model
{
    use HasFactory;

    protected $table = 'icd10_diseases';

    protected $fillable = [
        'icd10_code',
        'level',
        'disease_name_en',
    ];

    public function medicalDiagnoses()
    {
        return $this->belongsToMany(MedicalDiagnosis::class, 'medical_diagnosis_diseases', 'icd10_disease_id', 'medical_diagnosis_id');
    }
}
