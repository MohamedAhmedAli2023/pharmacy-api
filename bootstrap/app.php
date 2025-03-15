<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ForceJsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api([
            ForceJsonResponse::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
        $middleware->alias(['role' => \App\Http\Middleware\CheckRole::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
