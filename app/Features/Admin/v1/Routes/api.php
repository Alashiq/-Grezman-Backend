<?php

// ==========

use App\Features\Admin\v1\Controllers\AdminController;
use App\Features\Admin\v1\Controllers\AuthController;
use App\Features\Admin\v1\Controllers\HomeController;
use App\Features\Admin\v1\Controllers\RoleController;
use App\Features\Admin\v1\Controllers\UserController;
use App\Features\Admin\v1\Controllers\UserNotificationController;
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



    # # # # # # # # # # # # # # # # #  Admin  # # # # # # # # # # # # # # # # #
    Route::controller(AdminController::class)->prefix('admin')->group(
        function () {
            Route::get('/', [AdminController::class, 'index'])->middleware('check.role:ReadAdmin');
            Route::get('/new', [AdminController::class, 'new'])->middleware('check.role:CreateAdmin');
            Route::post('/', [AdminController::class, 'store'])->middleware('check.role:CreateAdmin');


            Route::get('/{admin}', [AdminController::class, 'show'])->middleware('check.role:ReadAdmin');
            Route::get('/{admin}/roles', [AdminController::class, 'showWithRoles'])->middleware('check.role:EditRoleAdmin');


            Route::put('/{admin}', [AdminController::class, 'edit'])->middleware('check.role:EditRoleAdmin');

            Route::delete('/{admin}', [AdminController::class, 'delete'])->middleware('check.role:DeleteAdmin');

            Route::put('/{admin}/active', [AdminController::class, 'active'])->middleware('check.role:ActiveAdmin');
            Route::put('/{admin}/disActive', [AdminController::class, 'disActive'])->middleware('check.role:DisActiveAdmin');
            Route::put('/{admin}/banned', [AdminController::class, 'banned'])->middleware('check.role:BannedAdmin');
            Route::put('/{admin}/reset', [AdminController::class, 'resetPassword'])->middleware('check.role:ResetPasswordAdmin');
        }
    );
    # # # # # # # # # # # # # # # # # End Admin  # # # # # # # # # # # # # # # 



    # # # # # # # # # # # # # # # # #  Admin Role  # # # # # # # # # # # # # # # # #
    Route::controller(RoleController::class)->prefix('role')->group(
        function () {
            Route::get('/', [RoleController::class, 'index'])->middleware('check.role:ReadRole');
            Route::get('/new', [RoleController::class, 'new'])->middleware('check.role:ReadAdmin');
            Route::delete('/{role}', [RoleController::class, 'delete'])->middleware('check.role:DeleteRole');
            Route::get('/{role}', [RoleController::class, 'show'])->middleware('check.role:ReadRole');
            Route::put('/{role}', [RoleController::class, 'edit'])->middleware('check.role:EditRole');
            Route::post('/', [RoleController::class, 'create'])->middleware('check.role:CreateRole');
        }
    );
    # # # # # # # # # # # # # # # # # End Admin Role  # # # # # # # # # # # # # # # 




    # # # # # # # # # # # # # # # # #  Users  # # # # # # # # # # # # # # # # #
    Route::controller(UserController::class)->prefix('user')->group(
        function () {
            Route::get('/', [UserController::class, 'index'])->middleware('check.role:ReadUser');
            Route::get('/{user}', [UserController::class, 'show'])->middleware('check.role:ReadUser');
            Route::put('/{user}/banned', [UserController::class, 'banned'])->middleware('check.role:BannedUser');
            Route::delete('/{user}', [UserController::class, 'delete'])->middleware('check.role:DeleteUser');
        }
    );
    # # # # # # # # # # # # # # # # # End Users  # # # # # # # # # # # # # # # 


    # # # # # # # # # # # # # # # # #  Users  # # # # # # # # # # # # # # # # #
    Route::controller(UserNotificationController::class)->prefix('usernotification')->group(
        function () {
            Route::get('/', [UserNotificationController::class, 'index'])->middleware('check.role:ReadUserNotification');
            Route::get('/{notification}', [UserNotificationController::class, 'show'])->middleware('check.role:ReadUserNotification');
            Route::delete('/{notification}', [UserNotificationController::class, 'delete'])->middleware('check.role:DeleteUserNotification');
        }
    );
    # # # # # # # # # # # # # # # # # End Users  # # # # # # # # # # # # # # # 



});
