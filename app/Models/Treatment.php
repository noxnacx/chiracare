<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $table = 'treatment';

    protected $fillable = [
        'checkin_id',
        'treatment_date',
        'treatment_status',
    ];
    public $timestamps = false;
    // ใน Treatment.php
    

    public function checkin()
    {
        return $this->belongsTo(Checkin::class, 'checkin_id');
    }

    public function medicalDiagnosis()
    {
        return $this->hasOne(MedicalDiagnosis::class, 'treatment_id');
    }
}
