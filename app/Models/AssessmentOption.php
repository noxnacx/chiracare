<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentOption extends Model
{
    use HasFactory;
    protected $table = 'assessment_option';
    protected $fillable = ['question_id', 'option_text', 'score'];

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }
}