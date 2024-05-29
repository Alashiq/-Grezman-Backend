<?php



use App\Features\App\v1\Controllers\AuthController;
use App\Features\App\v1\Controllers\ItemController;
use App\Features\App\v1\Controllers\UserNotificationController;
// ==========

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


# # # # # # # # # # # # # # # Login # # # # # # # # # # # # # # # 
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/activate', [AuthController::class, 'activate']);
# # # # # # # # # # # # # # # End Login # # # # # # # # # # # # # # # 

Route::middleware(['auth:sanctum', 'type.user'])->group(function () {

    # # # # # # # # # # # # # # # User # # # # # # # # # # # # # # # 
    Route::group(['prefix' => 'user'], function () {
        Route::post('/signup', [AuthController::class, 'signup']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/photo', [AuthController::class, 'photo']);
        Route::post('/name', [AuthController::class, 'name']);
    });
    # # # # # # # # # # # # # # # End User # # # # # # # # # # # # # # # 


    # # # # # # # # # # # # # # # Item # # # # # # # # # # # # # # # 
    Route::group(['prefix' => 'item'], function () {
        Route::get('/', [ItemController::class, 'index']);
        Route::get('/{id}', [ItemController::class, 'show']);
        Route::post('/', [ItemController::class, 'store']);
    });
    # # # # # # # # # # # # # # # End Item # # # # # # # # # # # # # # # 

        # # # # # # # # # # # # # # # Item # # # # # # # # # # # # # # # 
        Route::group(['prefix' => 'notification'], function () {
            Route::get('/', [UserNotificationController::class, 'index']);
        });
        # # # # # # # # # # # # # # # End Item # # # # # # # # # # # # # # # 
    


});
