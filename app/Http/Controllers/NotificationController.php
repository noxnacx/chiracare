<?php

// ========================
// เพิ่ม Methods ใน NotificationController
// ========================

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationController extends Controller
{
    // ... methods ที่มีอยู่แล้ว ...

    /**
     * ดึงสถิติการแจ้งเตือน (สำหรับ Admin)
     */
    public function getStatistics()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        try {
            // สถิติทั่วไป
            $total = Notification::count();
            $read = Notification::where('is_read', true)->count();
            $unread = Notification::where('is_read', false)->count();
            $urgent = Notification::where('priority', 'urgent')->count();

            // สถิติตาм role
            $byRole = Notification::select('target_role', DB::raw('count(*) as count'))
                                ->groupBy('target_role')
                                ->pluck('count', 'target_role')
                                ->toArray();

            // สถิติตาม priority
            $byPriority = Notification::select('priority', DB::raw('count(*) as count'))
                                    ->groupBy('priority')
                                    ->pluck('count', 'priority')
                                    ->toArray();

            // สถิติตาม type
            $byType = Notification::select('type', DB::raw('count(*) as count'))
                                ->groupBy('type')
                                ->orderBy('count', 'desc')
                                ->take(10)
                                ->pluck('count', 'type')
                                ->toArray();

            // สถิติรายวัน (7 วันที่ผ่านมา)
            $dailyStats = Notification::select(
                                DB::raw('DATE(created_at) as date'),
                                DB::raw('count(*) as count')
                            )
                            ->where('created_at', '>=', Carbon::now()->subDays(7))
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get()
                            ->keyBy('date')
                            ->toArray();

            // สถิติจำนวนผู้ใช้ที่ได้รับการแจ้งเตือน
            $userStats = [];
            foreach (Notification::ROLES as $role => $roleName) {
                $userCount = User::where('role', $role)->count();
                $notificationCount = Notification::where('target_role', $role)->count();
                $userStats[$role] = [
                    'role_name' => $roleName,
                    'user_count' => $userCount,
                    'notification_count' => $notificationCount,
                    'avg_per_user' => $userCount > 0 ? round($notificationCount / $userCount, 2) : 0
                ];
            }

            return response()->json([
                'success' => true,
                'total' => $total,
                'read' => $read,
                'unread' => $unread,
                'urgent' => $urgent,
                'by_role' => $byRole,
                'by_priority' => $byPriority,
                'by_type' => $byType,
                'daily_stats' => $dailyStats,
                'user_stats' => $userStats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงสถิติได้'
            ], 500);
        }
    }

    /**
     * ดึงการแจ้งเตือนล่าสุด (สำหรับ Admin)
     */
    public function getRecentNotifications(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        try {
            $limit = $request->input('limit', 20);
            
            $notifications = Notification::with('user')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($notification) {
             // เพิ่มข้อมูลจำนวนผู้ที่อ่านแล้ว
            $readCount = Notification::where('target_role', $notification->target_role)
            ->where('type', $notification->type)
            ->where('created_at', $notification->created_at)
            ->where('is_read', true)
            ->count();
                                           
            $recipientCount = User::where('role', $notification->target_role)->count();
                                           
            return [
            'id' => $notification->id,
            'target_role' => $notification->target_role,                                   'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'priority' => $notification->priority,
            'created_at' => $notification->created_at,
            'read_count' => $readCount,
            'recipient_count' => $recipientCount
            ];
            });

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลได้'
            ], 500);
        }
    }

    /**
     * ดึงรายการผู้ใช้ตาม Role (สำหรับ Admin)
     */
    public function getUsersByRole(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        try {
            $role = $request->input('role');
            
            if (!$role || !array_key_exists($role, Notification::ROLES)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role ไม่ถูกต้อง'
                ], 400);
            }

            $users = User::where('role', $role)
                        ->select('id', 'username', 'role', 'created_at')
                        ->with(['notifications' => function ($query) {
                            $query->where('created_at', '>=', Carbon::now()->subDays(30))
                                  ->select('user_id', 'is_read', 'created_at');
                        }])
                        ->get()
                        ->map(function ($user) {
                            $totalNotifications = $user->notifications->count();
                            $readNotifications = $user->notifications->where('is_read', true)->count();
                            $unreadNotifications = $totalNotifications - $readNotifications;
                            
                            return [
                                'id' => $user->id,
                                'username' => $user->username,
                                'role' => $user->role,
                                'created_at' => $user->created_at,
                                'notification_stats' => [
                                    'total' => $totalNotifications,
                                    'read' => $readNotifications,
                                    'unread' => $unreadNotifications
                                ]
                            ];
                        });

            return response()->json([
                'success' => true,
                'role' => $role,
                'role_name' => Notification::ROLES[$role],
                'users' => $users,
                'user_count' => $users->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถดึงข้อมูลผู้ใช้ได้'
            ], 500);
        }
    }

    /**
     * ส่งการแจ้งเตือนแบบ Broadcast (สำหรับ Admin)
     */
    public function broadcastNotification(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:' . implode(',', array_keys(Notification::PRIORITIES)),
            'exclude_roles' => 'nullable|array',
            'exclude_roles.*' => 'in:' . implode(',', array_keys(Notification::ROLES)),
            'type' => 'required|string|max:255',
            'data' => 'nullable|array'
        ]);

        try {
            $excludeRoles = $request->input('exclude_roles', []);
            
            $result = \App\Services\NotificationService::broadcastNotification(
                $request->type,
                $request->title,
                $request->message,
                $request->data ?? [],
                $request->priority,
                $excludeRoles
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'ส่งการแจ้งเตือนแบบ Broadcast สำเร็จ'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถส่งการแจ้งเตือนได้'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ลบการแจ้งเตือนหลายรายการ (สำหรับ Admin)
     */
    public function bulkDelete(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:notifications,id'
        ]);

        try {
            $deletedCount = Notification::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "ลบการแจ้งเตือน {$deletedCount} รายการสำเร็จ",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถลบการแจ้งเตือนได้'
            ], 500);
        }
    }

    /**
     * ทำเครื่องหมายอ่านหลายรายการ (สำหรับ Admin)
     */
    public function bulkMarkAsRead(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $request->validate([
            'target_role' => 'nullable|in:' . implode(',', array_keys(Notification::ROLES)),
            'type' => 'nullable|string',
            'priority' => 'nullable|in:' . implode(',', array_keys(Notification::PRIORITIES))
        ]);

        try {
            $query = Notification::where('is_read', false);

            if ($request->has('target_role')) {
                $query->where('target_role', $request->target_role);
            }

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            $updatedCount = $query->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => "ทำเครื่องหมายอ่าน {$updatedCount} รายการสำเร็จ",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถทำเครื่องหมายได้'
            ], 500);
        }
    }

    /**
     * ดึงรายงานประสิทธิภาพการแจ้งเตือน
     */
    public function getPerformanceReport(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        try {
            $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
            $endDate = $request->input('end_date', Carbon::now()->toDateString());

            // อัตราการเปิดอ่าน
            $openRates = DB::table('notifications')
                          ->select('target_role', 
                                  DB::raw('COUNT(*) as total'),
                                  DB::raw('SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read_count'))
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->groupBy('target_role')
                          ->get()
                          ->map(function ($item) {
                              return [
                                  'role' => $item->target_role,
                                  'total' => $item->total,
                                  'read_count' => $item->read_count,
                                  'open_rate' => $item->total > 0 ? round(($item->read_count / $item->total) * 100, 2) : 0
                              ];
                          });

            // เวลาเฉลี่ยในการเปิดอ่าน
            $avgReadTime = DB::table('notifications')
                            ->selectRaw('target_role, AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_minutes')
                            ->where('is_read', true)
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->groupBy('target_role')
                            ->get()
                            ->keyBy('target_role');

            // การแจ้งเตือนที่ได้รับความสนใจมากที่สุด
            $topNotifications = Notification::select('type', 'title', 'priority', 'target_role',
                                                   DB::raw('COUNT(*) as count'),
                                                   DB::raw('AVG(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read_rate'))
                                           ->whereBetween('created_at', [$startDate, $endDate])
                                           ->groupBy('type', 'title', 'priority', 'target_role')
                                           ->orderBy('read_rate', 'desc')
                                           ->take(10)
                                           ->get();

            // แนวโน้มการแจ้งเตือนรายวัน
            $dailyTrend = DB::table('notifications')
                           ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                           ->whereBetween('created_at', [$startDate, $endDate])
                           ->groupBy('date')
                           ->orderBy('date')
                           ->get();

            return response()->json([
                'success' => true,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'open_rates' => $openRates,
                'avg_read_time' => $avgReadTime,
                'top_notifications' => $topNotifications,
                'daily_trend' => $dailyTrend
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถสร้างรายงานได้'
            ], 500);
        }
    }

    public function checkNew()
    {
    $unread = Auth::user()->unreadNotifications;
    $latest = $unread->first();
    
    return response()->json([
        'has_new' => $unread->count() > 0,
        'count' => $unread->count(),
        'latest' => $latest ? [
            'id' => $latest->id,
            'title' => $latest->title,
            'message' => $latest->message,
            'priority' => $latest->priority
        ] : null
    ]);
    }

    public function markAsRead($id)
    {
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->update(['is_read' => true]);
    
    return response()->json(['success' => true]);
    }
    /**
     * ตั้งค่าการแจ้งเตือนอัตโนมัติ
     */
    public function setAutoNotificationRules(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $request->validate([
            'rules' => 'required|array',
            'rules.*.condition' => 'required|string',
            'rules.*.action' => 'required|array',
            'rules.*.enabled' => 'boolean'
        ]);

        try {
            // บันทึกกฎการแจ้งเตือนอัตโนมัติใน cache หรือ config
            cache()->put('notification_auto_rules', $request->rules, 86400); // 24 hours

            return response()->json([
                'success' => true,
                'message' => 'ตั้งค่ากฎการแจ้งเตือนอัตโนมัติสำเร็จ'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถตั้งค่าได้'
            ], 500);
        }
    }

    /**
     * ทดสอบการส่งการแจ้งเตือน
     */
    public function testNotification(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'ไม่มีสิทธิ์'], 403);
        }

        $request->validate([
            'target_role' => 'required|in:' . implode(',', array_keys(Notification::ROLES)),
            'test_user_id' => 'nullable|exists:users,id'
        ]);

        try {
            $testMessage = "🧪 การทดสอบระบบการแจ้งเตือน - " . now()->format('Y-m-d H:i:s');
            
            if ($request->has('test_user_id')) {
                // ทดสอบส่งให้ผู้ใช้เฉพาะคน
                $result = \App\Services\NotificationService::notifyUser(
                    $request->test_user_id,
                    'test',
                    '🧪 ทดสอบการแจ้งเตือน',
                    $testMessage,
                    ['test' => true, 'sent_by' => Auth::user()->username],
                    'normal'
                );
            } else {
                // ทดสอบส่งให้ role ทั้งหมด
                $result = \App\Services\NotificationService::notifyRole(
                    $request->target_role,
                    'test',
                    '🧪 ทดสอบการแจ้งเตือน',
                    $testMessage,
                    ['test' => true, 'sent_by' => Auth::user()->username],
                    'normal'
                );
            }

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'ส่งการแจ้งเตือนทดสอบสำเร็จ'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถส่งการแจ้งเตือนทดสอบได้'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPatientSummary()
    {
    $user = Auth::user();
    
    // สรุปผู้ป่วยวันนี้
    $todaySummary = $user->today_patient_summary;
    
    // การแจ้งเตือนอื่น ๆ
    $otherNotifications = $user->other_notifications;
    
    // จำนวนรวม
    $totalUnreadToday = $user->today_unread_patients_count;
    $totalOtherUnread = $user->customUnreadNotifications()
                             ->where('type', '!=', 'new_patient')
                             ->count();
    
    return response()->json([
        'today_patient_summary' => $todaySummary,
        'other_notifications' => $otherNotifications,
        'counts' => [
            'total_patients_today' => $totalUnreadToday,
            'total_other' => $totalOtherUnread,
            'grand_total' => $totalUnreadToday + $totalOtherUnread
        ]
    ]);
    }
}
