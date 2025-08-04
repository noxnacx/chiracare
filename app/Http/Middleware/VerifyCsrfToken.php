<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * Route ที่ไม่ต้องเช็ค CSRF Token
     */
    protected $except = [
        // 🎯 สำหรับ API หรือ webhook
        'api/*',
        'webhook/*',
        
        // ตัวอย่าง route อื่นๆ
        // 'stripe/*',
        // 'payment/callback',
    ];
}

