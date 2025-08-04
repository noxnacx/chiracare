<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     * Cookie ที่ไม่ต้องการให้เข้ารหัส
     */
    protected $except = [
        // ตัวอย่าง:
        // 'plain_cookie',
        // 'session_id',
    ];
}
