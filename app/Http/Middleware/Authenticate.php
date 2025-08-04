<?php
// =============================================================================
// 1. app/Http/Middleware/Authenticate.php
// =============================================================================

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // 🎯 แก้ไข route ตามที่ใช้ในโปรเจคของคุณ
        return $request->expectsJson() ? null : route('login');
        
        // หรือใช้แบบ URL ตรงๆ
        // return $request->expectsJson() ? null : '/login';
    }
}
