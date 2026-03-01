<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

file_put_contents(dirname(__DIR__).'/storage/logs/bootstrap_test.log', date('Y-m-d H:i:s').' - Bootstrap loaded'.PHP_EOL, FILE_APPEND);
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'peternak.only' => \App\Http\Middleware\PeternakOnly::class,
            'pengelola.admin.only' => \App\Http\Middleware\PengelolaAdminOnly::class,
            'admin.only' => \App\Http\Middleware\AdminOnly::class,
            'analytics.only' => \App\Http\Middleware\AnalyticsOnly::class,
            'track_session'  => \App\Http\Middleware\TrackUserSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
