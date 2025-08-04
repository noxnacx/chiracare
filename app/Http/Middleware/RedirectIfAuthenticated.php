<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * จัดการกับผู้ใช้ที่ login แล้ว (เช่น ไม่ให้เข้าหน้า login อีก)
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // 🎯 ถ้า login แล้ว redirect ไปหน้าไหน
                $user = Auth::user();
                
                // Redirect ตาม role
                switch ($user->role) {
                    case 'admin':
                        return redirect('/dashboard-admin');
                    case 'er':
                        return redirect('/er/dashboard');
                    case 'ipd':
                        return redirect('/ipd/dashboard');
                    case 'opd':
                        return redirect('/hospital/opd-dashboard');
                    case 'training_unit':
                        return redirect('/training/dashboard');
                    default:
                        return redirect('/dashboard');
                }
                
                // หรือใช้แบบง่ายๆ
                // return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
    
