<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminRedirect;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\TwoFactorAuth;
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
        $middleware->alias([
            'admin.guest' => AdminRedirect::class,
            'admin.auth' => AdminAuthenticate::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'department.access' => \App\Http\Middleware\CheckDepartmentAccess::class,
            // 'admin.guest' => \App\Http\Middleware\AdminAuthenticate::class,
            'dashboard.access' => \App\Http\Middleware\CheckDashboardAccess::class,
            '2fa' => TwoFactorAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
