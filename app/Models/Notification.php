<?php

// ========================
// 1. ปรับปรุง Notification Model
// ========================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'target_role',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'priority'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    // Available roles for notifications
    const ROLES = [
        'admin' => 'ผู้ดูแลระบบ',
        'opd' => 'แผนกผู้ป่วยนอก',
        'ipd' => 'แผนกผู้ป่วยใน',
        'er' => 'แผนกฉุกเฉิน',
        'training_unit' => 'หน่วยฝึก',
        'adminhospital' => 'ผู้ดูแลโรงพยาบาล'
    ];

    // Available priorities
    const PRIORITIES = [
        'low' => 'ต่ำ',
        'normal' => 'ปกติ',
        'high' => 'สูง',
        'urgent' => 'เร่งด่วน'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope สำหรับการกรองตาม role
    public function scopeForRole($query, $role)
    {
        return $query->where('target_role', $role);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function getRoleNameAttribute()
    {
        return self::ROLES[$this->target_role] ?? $this->target_role;
    }

    public function getPriorityNameAttribute()
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }
}