<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAnswer extends Model
{
    use HasFactory;
    protected $table = 'assessment_answer'; //
    protected $fillable = ['assessment_question_id', 'assessment_score_id', 'selected_option_id', 'answered_at']; //

    // ⭐️ เพิ่ม: ความสัมพันธ์กลับไปยัง Score หลักของการประเมิน
    public function assessmentScore()
    {
        return $this->belongsTo(AssessmentScore::class, 'assessment_score_id');
    }

    // ความสัมพันธ์ไปยังคำถาม (มีอยู่แล้ว ดีมากครับ)
    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id'); //
    }

    // ความสัมพันธ์ไปยังตัวเลือกที่ตอบ (มีอยู่แล้ว ดีมากครับ)
    public function option()
    {
        return $this->belongsTo(AssessmentOption::class, 'selected_option_id'); //
    }
}
