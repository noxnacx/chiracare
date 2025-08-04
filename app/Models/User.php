<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Notification;

class User extends Authenticatable // เปลี่ยนจาก Model เป็น Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
        'training_unit_id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with TrainingUnit
    public function trainingUnit()
    {
        return $this->belongsTo(TrainingUnit::class, 'training_unit_id');
    }

    // Check if user has specific role
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    // Check if user is training unit role
    public function isTrainingUnit()
    {
        return $this->role === 'training_unit';
    }

    // Override the username field for authentication
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    // 🚨 **เพิ่มส่วนนี้สำหรับ Custom Notifications**

    /**
     * Custom notifications relationship สำหรับระบบของเรา
     */
    public function customNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'user_id');
    }

    /**
     * การแจ้งเตือนที่ยังไม่อ่าน
     */
    public function customUnreadNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'user_id')->where('is_read', false);
    }

    /**
     * นับจำนวนการแจ้งเตือนที่ยังไม่อ่าน
     */
    public function getCustomUnreadCountAttribute()
    {
        return $this->customUnreadNotifications()->count();
    }

    /**
     * การแจ้งเตือนล่าสุด 5 รายการ
     */
    public function getRecentNotificationsAttribute()
    {
        return $this->customUnreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * การแจ้งเตือนเร่งด่วน
     */
    public function getUrgentNotificationsAttribute()
    {
        return $this->customUnreadNotifications()
            ->where('priority', 'urgent')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * สรุปการแจ้งเตือนผู้ป่วยตาม Risk Level วันนี้
     */
    public function getTodayPatientSummaryAttribute()
    {
        $today = now()->format('Y-m-d');

        $summary = $this->customUnreadNotifications()
            ->where('type', 'new_patient')
            ->whereDate('created_at', $today)
            ->get()
            ->groupBy(function ($notification) {
                $data = $notification->data;
                return $data['risk_level'] ?? 'unknown';
            })
            ->map(function ($notifications, $riskLevel) {
                return [
                    'count' => $notifications->count(),
                    'latest' => $notifications->sortByDesc('created_at')->first(),
                    'risk_level' => $riskLevel
                ];
            });

        // เรียงลำดับ red → yellow → green
        $ordered = collect([]);
        if ($summary->has('red'))
            $ordered['red'] = $summary['red'];
        if ($summary->has('yellow'))
            $ordered['yellow'] = $summary['yellow'];
        if ($summary->has('green'))
            $ordered['green'] = $summary['green'];

        return $ordered;
    }

    /**
     * ทำเครื่องหมายอ่านการแจ้งเตือนทั้งหมด
     */
    public function markAllNotificationsAsRead()
    {
        return $this->customUnreadNotifications()->update(['is_read' => true]);
    }

    /**
     * จำนวนผู้ป่วยที่ยังไม่อ่านทั้งหมดวันนี้
     */
    public function getTodayUnreadPatientsCountAttribute()
    {
        return $this->customUnreadNotifications()
            ->where('type', 'new_patient')
            ->whereDate('created_at', now()->format('Y-m-d'))
            ->count();
    }

    /**
     * การแจ้งเตือนอื่น ๆ (ไม่ใช่ new_patient)
     */
    public function getOtherNotificationsAttribute()
    {
        return $this->customUnreadNotifications()
            ->where('type', '!=', 'new_patient')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    }
}

