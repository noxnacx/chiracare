<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     * ฟิลด์ที่ไม่ต้องการให้ตัดช่องว่าง
     */
    protected $except = [
        // 🔐 ฟิลด์ password ไม่ควร trim
        'current_password',
        'password',
        'password_confirmation',
        
        // ตัวอย่างฟิลด์อื่นๆ
        // 'description', // หากต้องการเก็บช่องว่างใน description
        // 'content',     // หากต้องการเก็บช่องว่างใน content
    ];
}
