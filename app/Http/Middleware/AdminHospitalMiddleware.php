<?php

// app/Http/Middleware/AdminHospitalMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminHospitalMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ตรวจสอบว่าล็อกอินแล้วหรือไม่
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'กรุณาเข้าสู่ระบบก่อน'
                ], 401);
            }
            return redirect('/login')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        // ตรวจสอบ role - อนุญาต admin และ adminhospital
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'adminhospital'])) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'เฉพาะผู้ดูแลระบบโรงพยาบาลเท่านั้น'
                ], 403);
            }
            return redirect('/login')->with('error', 'เฉพาะผู้ดูแลระบบโรงพยาบาลเท่านั้น');
        }

        return $next($request);
    }
}