<?php

// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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

        // ตรวจสอบ role ว่าเป็น admin หรือไม่
        if (Auth::user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'เฉพาะผู้ดูแลระบบเท่านั้น'
                ], 403);
            }
            return redirect('/login')->with('error', 'เฉพาะผู้ดูแลระบบเท่านั้น');
        }

        return $next($request);
    }
}