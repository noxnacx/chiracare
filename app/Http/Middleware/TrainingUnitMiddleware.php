<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrainingUnitMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $this->unauthorizedResponse($request, 'กรุณาเข้าสู่ระบบก่อน');
        }

        $user = Auth::user();
        
        if ($user->role !== 'training_unit') {
            return $this->forbiddenResponse($request, 'เฉพาะหน่วยฝึกเท่านั้น');
        }

        // ⚠️ เช็คเพิ่มเติม: ต้องมี training_unit_id
        if (!$user->training_unit_id) {
            return $this->forbiddenResponse($request, 'ไม่พบข้อมูลหน่วยฝึกของคุณ กรุณาติดต่อผู้ดูแลระบบ');
        }

        return $next($request);
    }

    // Helper methods เหมือนเดิม...
}

