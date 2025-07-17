<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentMentalHealth extends Model
{
    use HasFactory;
    protected $table = 'appointments_mental_health';
    protected $guarded = ['id'];

    public function tracking()
    {
        return $this->belongsTo(AssessmentStatusTracking::class, 'status_tracking_id');
    }

    // หนึ่งการนัดหมาย จะมีหนึ่งการรักษา
    public function treatment()
    {
        return $this->hasOne(TreatmentMentalHealth::class, 'appointment_id');
    }
}
