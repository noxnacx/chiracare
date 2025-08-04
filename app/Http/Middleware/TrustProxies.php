<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     * Proxy servers ที่เชื่อถือได้
     */
    protected $proxies = [
        // 🌐 สำหรับ production ใช้ proxy servers
        // '192.168.1.1',
        // '10.0.0.0/8',
        
        // สำหรับ development ทั้งหมด
        // '*', // ⚠️ ใช้เฉพาะ development เท่านั้น!
    ];

    /**
     * The headers that should be used to detect proxies.
     * Headers ที่ใช้ตรวจสอบ proxy
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
