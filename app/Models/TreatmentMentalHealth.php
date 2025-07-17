<?php

namespace App\Models; // ✅ แก้ไข: เพิ่ม Namespace ให้ถูกต้องตามโครงสร้าง Laravel

use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ เพิ่ม HasFactory
use Illuminate\Database\Eloquent\Model;

class TreatmentMentalHealth extends Model
{
    use HasFactory; // ✅ เพิ่ม HasFactory

    protected $table = 'treatment_mental_health';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // ✅ [ส่วนที่แก้ไขสำคัญ] เพิ่ม $fillable เพื่ออนุญาตให้บันทึกข้อมูลได้ทุกคอลัมน์ที่จำเป็น
    protected $fillable = [
        'appointment_id',
        'treatment_date',
        'doctor_name',
        'medicine_name',
        'notes', // <--- อนุญาตให้บันทึก notes แล้ว
    ];

    /**
     * Get the appointment that owns the treatment.
     */
    public function appointment()
    {
        return $this->belongsTo(AppointmentMentalHealth::class, 'appointment_id');
    }
}
