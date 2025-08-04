<?php
namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * ส่งการแจ้งเตือนไปยัง Role ที่ระบุ
     */
    public static function notifyRole($targetRole, $type, $title, $message, $data = [], $priority = 'normal')
    {
        try {
            // ดึงผู้ใช้ที่มี role ตามที่ระบุ
            $users = User::where('role', $targetRole)->get();

            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'target_role' => $targetRole,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                    'priority' => $priority
                ]);
            }

            Log::info('Notification sent to role', [
                'target_role' => $targetRole,
                'type' => $type,
                'user_count' => $users->count()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification to role: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ส่งการแจ้งเตือนไปยังหลาย Role
     */
    public static function notifyMultipleRoles($targetRoles, $type, $title, $message, $data = [], $priority = 'normal')
    {
        foreach ($targetRoles as $role) {
            self::notifyRole($role, $type, $title, $message, $data, $priority);
        }
    }

    /**
     * ส่งการแจ้งเตือนไปยังผู้ใช้เฉพาะ
     */
    public static function notifyUser($userId, $type, $title, $message, $data = [], $priority = 'normal')
    {
        try {
            $user = User::findOrFail($userId);

            Notification::create([
                'user_id' => $user->id,
                'target_role' => $user->role,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'priority' => $priority
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification to user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ส่งการแจ้งเตือนเมื่อมีผู้ป่วยใหม่
     */
    public static function notifyNewPatient($medicalReport)
    {
        $soldier = $medicalReport->soldier;
        $riskLevel = $medicalReport->vitalSign->risk_level ?? 'unknown';
        
        // กำหนดระดับความสำคัญและ target roles ตาม risk level
        $priority = match($riskLevel) {
            'red' => 'urgent',
            'yellow' => 'high',
            'green' => 'normal',
            default => 'normal'
        };

        // กำหนด target roles ตาม risk level
        $targetRoles = match($riskLevel) {
            'red' => ['adminhospital'], // เคสฉุกเฉิน -> Admin Hospital และ ER
            'yellow' => ['adminhospital'], // เฝ้าระวัง -> Admin Hospital และ OPD
            'green' => ['adminhospital'], // ปกติ -> Admin Hospital เท่านั้น
            default => ['adminhospital']
        };

        $riskText = match($riskLevel) {
            'red' => '🔴 ฉุกเฉิน',
            'yellow' => '🟡 เฝ้าระวัง', 
            'green' => '🟢 ปกติ',
            default => '⚪ ไม่ทราบ'
        };

        $title = "ผู้ป่วยใหม่ - {$riskText}";
        $message = "ทหาร {$soldier->first_name} {$soldier->last_name} ".
        "จาก " . ($soldier->trainingUnit->unit_name ?? 'ไม่ระบุหน่วย') . " ".
        "ได้ส่งรายงานการรักษาเข้ามา\n".
        "อาการ: {$medicalReport->symptom_description}";
        
        // ✅ เพิ่ม metadata สำหรับการจัดกลุ่ม
        $data = [
        'medical_report_id' => $medicalReport->id,
        'soldier_id' => $soldier->id,
        'soldier_name' => $soldier->first_name . ' ' . $soldier->last_name,
        'training_unit' => $soldier->trainingUnit->unit_name ?? null,
        'risk_level' => $riskLevel, // 🚨 สำคัญสำหรับการจัดกลุ่ม
        'symptom' => $medicalReport->symptom_description,
        'created_at' => $medicalReport->created_at->toISOString(),
        'notification_date' => now()->format('Y-m-d') // 🚨 สำหรับกรองวันที่
        ];


        // ส่งการแจ้งเตือนไปยัง roles ที่เกี่ยวข้อง
        return self::notifyMultipleRoles($targetRoles, 'new_patient', $title, $message, $data, $priority);
    }

    /**
     * ส่งการแจ้งเตือนเมื่อมีการนัดหมาย
     */
    public static function notifyNewAppointment($appointment)
    {
        $medicalReport = $appointment->medicalReport;
        $soldier = $medicalReport->soldier;

        $title = "นัดหมายใหม่";
        $message = "มีการนัดหมายใหม่สำหรับ {$soldier->first_name} {$soldier->last_name} ".
                  "วันที่ {$appointment->appointment_date->format('d/m/Y H:i')}";

        $data = [
            'appointment_id' => $appointment->id,
            'medical_report_id' => $medicalReport->id,
            'soldier_name' => $soldier->first_name . ' ' . $soldier->last_name,
            'appointment_date' => $appointment->appointment_date->toISOString(),
            'case_type' => $appointment->case_type
        ];

        $priority = $appointment->case_type === 'critical' ? 'urgent' : 'normal';
        
        // กำหนด target roles ตาม case type
        $targetRoles = match($appointment->case_type) {
            'critical' => ['adminhospital', 'er'],
            'urgent' => ['adminhospital', 'opd'],
            default => ['adminhospital']
        };

        return self::notifyMultipleRoles($targetRoles, 'new_appointment', $title, $message, $data, $priority);
    }

    /**
     * ส่งการแจ้งเตือนเมื่อมีผู้ป่วย Admit
     */
    public static function notifyPatientAdmit($diagnosis)
    {
        $treatment = $diagnosis->treatment;
        $soldier = $treatment->checkin->appointment->medicalReport->soldier;

        $title = "ผู้ป่วยใน (Admit)";
        $message = "ทหาร {$soldier->first_name} {$soldier->last_name} ".
                  "ได้รับการ Admit เข้ารักษาในแผนก {$diagnosis->department_type}";

        $data = [
            'diagnosis_id' => $diagnosis->id,
            'soldier_name' => $soldier->first_name . ' ' . $soldier->last_name,
            'department_type' => $diagnosis->department_type,
            'treatment_status' => $diagnosis->treatment_status
        ];

        // ส่งการแจ้งเตือนไปยัง Admin Hospital และ IPD
        $targetRoles = ['adminhospital', 'ipd'];

        return self::notifyMultipleRoles($targetRoles, 'patient_admit', $title, $message, $data, 'high');
    }

    /**
     * ส่งการแจ้งเตือนเมื่อมีการปฏิเสธหรือยกเลิกนัดหมาย
     */
    public static function notifyAppointmentRejected($appointment, $reason = '')
    {
        $medicalReport = $appointment->medicalReport;
        $soldier = $medicalReport->soldier;

        $title = "นัดหมายถูกปฏิเสธ";
        $message = "นัดหมายของ {$soldier->first_name} {$soldier->last_name} ถูกปฏิเสธ" .
                  ($reason ? "\nเหตุผล: {$reason}" : "");

        $data = [
            'appointment_id' => $appointment->id,
            'soldier_name' => $soldier->first_name . ' ' . $soldier->last_name,
            'reason' => $reason
        ];

        // ส่งกลับไปยัง Training Unit
        return self::notifyRole('training_unit', 'appointment_rejected', $title, $message, $data, 'high');
    }

    /**
     * ส่งการแจ้งเตือนเมื่อการรักษาเสร็จสิ้น
     */
    public static function notifyTreatmentCompleted($diagnosis)
    {
        $treatment = $diagnosis->treatment;
        $soldier = $treatment->checkin->appointment->medicalReport->soldier;

        $title = "การรักษาเสร็จสิ้น";
        $message = "การรักษา {$soldier->first_name} {$soldier->last_name} เสร็จสิ้นแล้ว";

        $data = [
            'diagnosis_id' => $diagnosis->id,
            'soldier_name' => $soldier->first_name . ' ' . $soldier->last_name,
            'treatment_result' => $diagnosis->treatment_status
        ];

        // ส่งไปยัง Admin Hospital และ Training Unit
        $targetRoles = ['adminhospital', 'training_unit'];

        return self::notifyMultipleRoles($targetRoles, 'treatment_completed', $title, $message, $data, 'normal');
    }

    /**
     * ส่งการแจ้งเตือนแบบ Broadcast ไปทุก Role
     */
    public static function broadcastNotification($type, $title, $message, $data = [], $priority = 'normal', $excludeRoles = [])
    {
        $allRoles = array_keys(Notification::ROLES);
        $targetRoles = array_diff($allRoles, $excludeRoles);

        return self::notifyMultipleRoles($targetRoles, $type, $title, $message, $data, $priority);
    }

    /**
     * ส่งการแจ้งเตือนเมื่อระบบมีปัญหา
     */
    public static function notifySystemAlert($alertType, $message, $severity = 'high')
    {
        $title = "แจ้งเตือนระบบ";
        
        $data = [
            'alert_type' => $alertType,
            'timestamp' => now()->toISOString(),
            'severity' => $severity
        ];

        // ส่งไปยัง Admin เท่านั้น
        return self::notifyRole('admin', 'system_alert', $title, $message, $data, $severity === 'critical' ? 'urgent' : 'high');
    }
}