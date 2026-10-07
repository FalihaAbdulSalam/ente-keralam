<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
            'admin.guest' => \App\Http\Middleware\AdminGuest::class,
            'admin.super_admin_only' => \App\Http\Middleware\AdminSuperAdminOnly::class,
        ]);
        
        // Apply cache headers to all web requests
        $middleware->web(append: [
            \App\Http\Middleware\SetCacheHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Catch RouteNotFoundException as fallback for unauthenticated admin access
        $exceptions->render(function (\Symfony\Component\Routing\Exception\RouteNotFoundException $e, $request) {
            if ($request->is('admin/*') && !$request->is('admin/login')) {
                return redirect('/');
            }
        });
    })->create();

if ($storagePath = env('APP_STORAGE')) {
    $app->useStoragePath($storagePath);
}

return $app;
