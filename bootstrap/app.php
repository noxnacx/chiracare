<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Register middleware aliases ตาม Laravel 12 Documentation
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'opd' => \App\Http\Middleware\OpdMiddleware::class,
            'er' => \App\Http\Middleware\ErMiddleware::class,
            'ipd' => \App\Http\Middleware\IpdMiddleware::class,
            'training_unit' => \App\Http\Middleware\TrainingUnitMiddleware::class,
            'adminhospital' => \App\Http\Middleware\AdminHospitalMiddleware::class,
        ]);

        // ✅ หรือสร้าง middleware group
        $middleware->group('admin', [
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();