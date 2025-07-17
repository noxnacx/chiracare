<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;
    protected $table = 'assessment_question';
    protected $fillable = ['assessment_type', 'question_text'];

    public function options()
    {
        return $this->hasMany(AssessmentOption::class, 'question_id');
    }
}
