<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    use HasFactory;
    protected $table = 'assessment_score';

    // ✅ เพิ่ม 'assessment_type' เข้าไปใน $fillable
    protected $fillable = ['soldier_id', 'assessment_type', 'total_score', 'risk_level', 'assessment_date'];

    public $timestamps = true;

    // ความสัมพันธ์กลับไปยังเจ้าของผลประเมิน (ทหาร)
    public function soldier()
    {
        return $this->belongsTo(Soldier::class, 'soldier_id');
    }

    // ความสัมพันธ์ไปยังคำตอบทั้งหมดในประเมินครั้งนี้
    public function answers()
    {
        return $this->hasMany(AssessmentAnswer::class, 'assessment_score_id');
    }

    // ความสัมพันธ์เพื่อดึง "ประเภท" ของแบบประเมิน (ยังคงเก็บไว้ได้ แต่เราจะใช้คอลัมน์ใหม่เป็นหลัก)
    public function assessmentType()
    {
        return $this->hasOneThrough(
            AssessmentQuestion::class,
            AssessmentAnswer::class,
            'assessment_score_id',
            'id',
            'id',
            'assessment_question_id'
        )->select('assessment_type');
    }


}
