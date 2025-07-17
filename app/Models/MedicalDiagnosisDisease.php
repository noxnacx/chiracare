<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalDiagnosisDisease extends Model
{
    use HasFactory;

    protected $table = 'medical_diagnosis_diseases';

    protected $fillable = [
        'medical_diagnosis_id',
        'icd10_disease_id',
    ];
}
