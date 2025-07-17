<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rotation extends Model
{
    use HasFactory;

    // กำหนดชื่อของตาราง (ถ้าชื่อตารางไม่ตรงกับชื่อ default ใน Laravel)
    protected $table = 'rotation';

    // กำหนดว่าเราไม่ต้องการให้ใช้ 'timestamps' ถ้าไม่ใช้ created_at, updated_at
    public $timestamps = true;

    // ระบุว่า column ที่สามารถกรอกข้อมูลได้มีอะไรบ้าง
    protected $fillable = [
        'rotation_name',
        'status', // default เป็น 'active' หรือ 'inactive'
    ];

    // ถ้าต้องการเปลี่ยนชื่อของ 'created_at' และ 'updated_at' ให้ตรงกับฐานข้อมูลที่คุณกำหนดเอง
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}


