<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSign extends Model
{
    use HasFactory;

    protected $fillable = [
        'temperature',
        'blood_pressure',
        'heart_rate',
        'source',
        'risk_level',
    ];
}
