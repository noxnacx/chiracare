<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IPDMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $this->unauthorizedResponse($request, 'กรุณาเข้าสู่ระบบก่อน');
        }

        if (Auth::user()->role !== 'ipd') {
            return $this->forbiddenResponse($request, 'เฉพาะแผนกผู้ป่วยในเท่านั้น');
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
