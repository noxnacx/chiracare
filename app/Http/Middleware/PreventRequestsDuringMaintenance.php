<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     * Route ที่ยังเข้าได้ขณะ maintenance mode
     */
    protected $except = [
        // 🛠️ Route ที่ยังใช้งานได้ขณะ maintenance
        // '/admin/*',      // Admin ยังเข้าได้
        // '/health-check', // Health check ยังใช้ได้
        // '/status',       // Status page ยังใช้ได้
    ];
}
