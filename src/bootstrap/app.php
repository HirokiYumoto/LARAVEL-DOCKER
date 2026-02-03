<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// ★追加
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ★ここに追加： 'admin' という名前で使えるようにする
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'owner' => \App\Http\Middleware\OwnerMiddleware::class, // (もし以前作っていれば)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();