<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Only the nginx sidecar may set X-Forwarded-For. Trusting "*" would let
        // any visitor forge request()->ip() and mint unlimited rate-limit buckets
        // for the intake form. Private ranges cover the compose bridge network
        // whatever subnet Docker hands it; php-fpm's port is never published, so
        // nothing outside the network can reach it directly. nginx also pins the
        // header to $remote_addr, so a forged one never gets this far.
        $middleware->trustProxies(at: [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
