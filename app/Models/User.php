<?php

// ในไฟล์ app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // ใช้ Authenticatable แทน Model
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable // เปลี่ยนจาก Model เป็น Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['username', 'password', 'role', 'training_unit_id'];

    public function trainingUnit()
    {
        return $this->belongsTo(TrainingUnit::class, 'training_unit_id');
    }

    // หากต้องการกำหนดการทำงานของ timestamps หรืออื่น ๆ สามารถกำหนดได้ที่นี่
}