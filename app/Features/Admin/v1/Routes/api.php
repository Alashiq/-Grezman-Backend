<?php

// ==========

use App\Features\Admin\v1\Controllers\AuthController;
use App\Features\Admin\v1\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['auth:sanctum', 'type.admin'])->group(function () {

    # # # # # # # # # # # # # # # Admin Auth # # # # # # # # # # # # # # # 
    Route::group(
        ['prefix' => 'auth'],
        function () {
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/password', [AuthController::class, 'password']);
            Route::post('/name', [AuthController::class, 'name']);
            Route::post('/photo', [AuthController::class, 'photo']);


        }
    );
    # # # # # # # # # # # # # # # End Admin Auth # # # # # # # # # # # # # # # 

        # # # # # # # # # # # # # # # # #  Home  # # # # # # # # # # # # # # # # #
        Route::controller(HomeController::class)->prefix('home')->group(
            function () {
                Route::get('/', [HomeController::class, 'index'])->middleware('check.role:HomeChart');
            }
        );
        # # # # # # # # # # # # # # # # # End Home  # # # # # # # # # # # # # # # 

});
