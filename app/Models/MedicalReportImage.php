<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalReportImage extends Model
{
    use HasFactory;

    protected $table = 'medical_report_images'; // ชื่อตารางที่ถูกต้องในฐานข้อมูล

    protected $fillable = [
        'medical_report_id',
        'image_type',
        'image_symptom', // เปลี่ยนจาก image เป็น image_symptom
    ];

    public function report()
    {
        return $this->belongsTo(MedicalReport::class, 'medical_report_id');
    }

    public $timestamps = true;
}
