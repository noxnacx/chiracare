<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentStatusTracking extends Model
{
    use HasFactory;
    protected $table = 'assessment_status_tracking';
    protected $guarded = ['id']; // อนุญาตให้ mass assign ทุก field ยกเว้น id

    public function soldier()
    {
        return $this->belongsTo(Soldier::class, 'soldier_id');
    }

    public function assessmentScore()
    {
        return $this->belongsTo(AssessmentScore::class, 'assessment_score_id');
    }

    // หนึ่งเคสการติดตาม อาจมีการนัดหมายได้หลายครั้ง
    public function appointments()
    {
        return $this->hasMany(AppointmentMentalHealth::class, 'status_tracking_id');
    }
}
