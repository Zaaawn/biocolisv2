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
    ->withMiddleware(function (Middleware $middleware) {

        // Middleware global (s'applique à toutes les requêtes authentifiées)
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\CheckNotBanned::class,
        ]);

        // Alias pour les routes
        $middleware->alias([
            'role'   => \App\Http\Middleware\CheckRole::class,
            'stripe' => \App\Http\Middleware\CheckStripeAccount::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
