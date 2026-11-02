<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies on Vercel
        $middleware->trustProxies(at: '*'); 

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Ensure writable storage directory on Serverless (Vercel)
if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || getenv('VERCEL') || env('APP_ENV') === 'production') {
    $storagePath = '/tmp/storage';
    if (!is_dir($storagePath . '/framework/views')) {
        @mkdir($storagePath . '/framework/views', 0777, true);
        @mkdir($storagePath . '/framework/sessions', 0777, true);
        @mkdir($storagePath . '/framework/cache/data', 0777, true);
        @mkdir($storagePath . '/logs', 0777, true);
        @mkdir($storagePath . '/app/public', 0777, true);
    }
    $app->useStoragePath($storagePath);
}

return $app;
