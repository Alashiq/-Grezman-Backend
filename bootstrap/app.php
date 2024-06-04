<?php

use App\Features\Admin\v1\Middleware\AuthAdminMiddleware;
use App\Features\Admin\v1\Middleware\CheckAdminRoleMiddleware;
use App\Features\App\v1\Middleware\AuthAppMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::namespace('AppApiV1')->name('app.')->prefix('app/v1/api')->group(base_path('app/Features/App/v1/Routes/api.php'));
            Route::namespace('AdminApiV1')->name('admin.')->prefix('admin/v1/api')->group(base_path('app/Features/Admin/v1/Routes/api.php'));

        },


        // function(Router $router){
        //     Route::middleware('api')
        //     ->prefix('app')
        //     ->group(base_path('app/Features/App/Routes/api_v1.php'));
        // },

        // commands: __DIR__.'/../routes/console.php',


    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'type.user' => AuthAppMiddleware::class,
            'type.admin' => AuthAdminMiddleware::class,
            'check.role' => CheckAdminRoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e) {
            return response()->json(['success' => false, 'message' => 'انتهت الجلسة الخاصة بك, أعد عمل تسجيل دخول'],401);
        });
    })->create();
