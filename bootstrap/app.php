<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Router;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('app/v1/api')->group(base_path('app/Features/App/v1/Routes/api.php'));
        },


        // function(Router $router){
        //     Route::middleware('api')
        //     ->prefix('app')
        //     ->group(base_path('app/Features/App/Routes/api_v1.php'));
        // },

        // commands: __DIR__.'/../routes/console.php',


    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
