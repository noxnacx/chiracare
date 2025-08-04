<?php
class NotificationHelper
{
    public static function getRiskLevelEmoji($riskLevel)
    {
        return match ($riskLevel) {
            'red' => '🔴',
            'yellow' => '🟡',
            'green' => '🟢',
            default => '⚪'
        };
    }

    public static function getRiskLevelText($riskLevel)
    {
        return match ($riskLevel) {
            'red' => 'ฉุกเฉิน',
            'yellow' => 'เฝ้าระวัง',
            'green' => 'ปกติ',
            default => 'ไม่ทราบ'
        };
    }

    public static function getRiskLevelClass($riskLevel)
    {
        return match ($riskLevel) {
            'red' => 'text-danger',
            'yellow' => 'text-warning',
            'green' => 'text-success',
            default => 'text-muted'
        };
    }
}