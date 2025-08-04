<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LoginLog;

class AuthController extends Controller
{
    public function __construct()
    {
        // ✅ Comment guest middleware ออกก่อน
        // $this->middleware('guest')->only(['showLoginForm']);

        // ✅ Admin middleware ใช้ inline แทน
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน'], 401);
                }
                return redirect('/login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
            }

            if (Auth::user()->role !== 'admin') {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'เฉพาะ Admin เท่านั้น'], 403);
                }
                return redirect('/login')->with('error', 'เฉพาะ Admin เท่านั้น');
            }

            return $next($request);
        })->only(['showLoginLogs', 'getLoginLogs']);
    }

    public function showLoginForm()
    {
    // ✅ ลบทั้งหมด เหลือแค่บรรทัดนี้
    return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:255',
                'password' => 'required|string',
            ]);

            $user = User::with('trainingUnit')->where('username', $request->username)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user);

                $this->logLogin($user, $request, 'success');

                // ✅ ส่ง redirect_url กลับไป
                return response()->json([
                    'success' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                        'training_unit' => $user->trainingUnit?->unit_name
                    ],
                    'redirect_url' => $this->getRedirectUrl($user->role)
                ]);
            }

            if ($user) {
                $this->logLogin($user, $request, 'failed');
            } else {
                $this->logFailedLogin($request->username, $request);
            }

            return response()->json([
                'success' => false,
                'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'
            ], 401);

        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในระบบ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($user) {
                $this->logLogin($user, $request, 'logout');
            }

            return response()->json([
                'success' => true,
                'message' => 'ออกจากระบบสำเร็จ',
                'redirect_url' => '/login'
            ]);
        } catch (\Exception $e) {
            \Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการออกจากระบบ'
            ], 500);
        }
    }

    public function checkAuth()
    {
        try {
            if (Auth::check()) {
                $user = Auth::user()->load('trainingUnit');
                return response()->json([
                    'success' => true,
                    'authenticated' => true,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'role' => $user->role,
                        'training_unit' => $user->trainingUnit?->unit_name,
                        'dashboard_url' => $this->getRedirectUrl($user->role)
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'authenticated' => false
            ]);
        } catch (\Exception $e) {
            \Log::error('Check auth error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์'
            ], 500);
        }
    }

    // ✅ Helper methods
    private function getRedirectUrl(string $role): string
    {
        return match($role) {
            'admin' => '/admin/overview',
            'adminhospital' => '/dashboard-admin',
            'er' => '/er/dashboard',
            'ipd' => '/ipd/dashboard',
            'opd' => '/hospital/opd-dashboard',
            'training_unit' => '/training/dashboard',
            default => '/dashboard'
        };
    }

    private function logLogin(User $user, Request $request, string $status): void
    {
        try {
            if (class_exists('App\Models\LoginLog')) {
                LoginLog::create([
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => $status,
                    'training_unit_name' => $user->trainingUnit?->unit_name,
                    'login_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to log login: ' . $e->getMessage());
        }
    }

    private function logFailedLogin(string $username, Request $request): void
    {
        try {
            if (class_exists('App\Models\LoginLog')) {
                LoginLog::create([
                    'user_id' => 0,
                    'username' => $username,
                    'role' => 'unknown',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'failed',
                    'training_unit_name' => null,
                    'login_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to log failed login: ' . $e->getMessage());
        }
    }
}
