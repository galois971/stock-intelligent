<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Build the application instance
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (Render/Cloudflare) and use forwarded headers
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
