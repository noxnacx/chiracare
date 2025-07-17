<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingUnit extends Model
{
    use HasFactory;

    protected $table = 'training_unit'; // ✅ กำหนดชื่อตารางให้ตรงกับฐานข้อมูล
    protected $primaryKey = 'id'; // ✅ ระบุ Primary Key
    public $timestamps = true; // ✅ เปิดใช้งาน timestamps

    protected $fillable = [
        'unit_name',
        'status',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public function soldiers()
    {
        return $this->hasMany(Soldier::class, 'training_unit_id');
    }
}
