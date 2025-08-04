<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return $this->unauthorizedResponse($request, 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();
        
        if (!in_array($user->role, $roles)) {
            $allowedRoles = implode(', ', $roles);
            return $this->forbiddenResponse($request, "เฉพาะ {$allowedRoles} เท่านั้น");
        }

        // เช็คเพิ่มเติมสำหรับ training_unit
        if ($user->role === 'training_unit' && !$user->training_unit_id) {
            return $this->forbiddenResponse($request, 'ไม่พบข้อมูลหน่วยฝึกของคุณ');
        }

        return $next($request);
    }

    private function unauthorizedResponse($request, $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 401);
        }
        return redirect('/login')->with('error', $message);
    }

    private function forbiddenResponse($request, $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 403);
        }
        return redirect('/login')->with('error', $message);
    }
    // Helper methods เหมือนเดิม...
}