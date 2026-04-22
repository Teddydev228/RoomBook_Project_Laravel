<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS middleware
        $middleware->appendToGroup('web', [
            HandleCors::class,
        ]);
        
        $middleware->appendToGroup('api', [
            HandleCors::class,
        ]);
        
        // Custom API auth middleware
        $middleware->alias([
            'auth.api' => ApiAuthMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
