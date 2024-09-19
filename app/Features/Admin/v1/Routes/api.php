<?php

// ==========

use App\Features\Admin\v1\Controllers\AdminController;
use App\Features\Admin\v1\Controllers\AuthController;
use App\Features\Admin\v1\Controllers\BannerController;
use App\Features\Admin\v1\Controllers\BatchController;
use App\Features\Admin\v1\Controllers\CompanyController;
use App\Features\Admin\v1\Controllers\HomeController;
use App\Features\Admin\v1\Controllers\JoinController;
use App\Features\Admin\v1\Controllers\ProfileController;
use App\Features\Admin\v1\Controllers\RoleController;
use App\Features\Admin\v1\Controllers\SaleController;
use App\Features\Admin\v1\Controllers\TowerController;
use App\Features\Admin\v1\Controllers\TransactionController;
use App\Features\Admin\v1\Controllers\UserController;
use App\Features\Admin\v1\Controllers\UserNotificationController;
use App\Features\Admin\v1\Controllers\VoucherController;
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
            Route::get('/new', [RoleController::class, 'new'])->middleware('check.role:CreateRole');
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
            Route::get('/new', [UserNotificationController::class, 'new'])->middleware('check.role:CreateUserNotification');
            Route::get('/{notification}', [UserNotificationController::class, 'show'])->middleware('check.role:ReadUserNotification');
            Route::delete('/{notification}', [UserNotificationController::class, 'delete'])->middleware('check.role:DeleteUserNotification');
            Route::post('/', [UserNotificationController::class, 'store'])->middleware('check.role:CreateUserNotification');
        }
    );
    # # # # # # # # # # # # # # # # # End Users  # # # # # # # # # # # # # # # 



    # # # # # # # # # # # # # # # # #  Company  # # # # # # # # # # # # # # # # #
    Route::controller(CompanyController::class)->prefix('company')->group(
        function () {
            Route::get('/', [CompanyController::class, 'index'])->middleware('check.role:ReadCompany');
            Route::get('/new', [CompanyController::class, 'new'])->middleware('check.role:CreateCompany');
            Route::post('/', [CompanyController::class, 'store'])->middleware('check.role:CreateCompany');

            Route::get('/{company}', [CompanyController::class, 'show'])->middleware('check.role:ReadCompany');
            Route::delete('/{company}', [CompanyController::class, 'delete'])->middleware('check.role:DeleteCompany');
            Route::post('/{company}/edit', [CompanyController::class, 'edit'])->middleware('check.role:EditCompany');
        }
    );
    # # # # # # # # # # # # # # # # # End Company  # # # # # # # # # # # # # # # 


    # # # # # # # # # # # # # # # # #  Batch  # # # # # # # # # # # # # # # # #
    Route::controller(BatchController::class)->prefix('batch')->group(
        function () {
            Route::get('/', [BatchController::class, 'index'])->middleware('check.role:ReadBatch');
            Route::get('/new', [BatchController::class, 'new'])->middleware('check.role:CreateBatch');
            Route::post('/', [BatchController::class, 'store'])->middleware('check.role:CreateCompany');

            Route::get('/{batch}', [BatchController::class, 'show'])->middleware('check.role:ReadBatch');
            Route::delete('/{batch}', [BatchController::class, 'delete'])->middleware('check.role:DeleteBatch');
            Route::post('/{batch}/edit', [BatchController::class, 'edit'])->middleware('check.role:EditBatch');
        }
    );
    # # # # # # # # # # # # # # # # # End Batch  # # # # # # # # # # # # # # # 


    # # # # # # # # # # # # # # # # #  Voucher  # # # # # # # # # # # # # # # # #
    Route::controller(VoucherController::class)->prefix('voucher')->group(
        function () {
            Route::get('/', [VoucherController::class, 'index'])->middleware('check.role:ReadVoucher');
            Route::get('/new', [VoucherController::class, 'new'])->middleware('check.role:CreateVoucher');
            Route::post('/', [VoucherController::class, 'store'])->middleware('check.role:CreateVoucher');

            Route::get('/{company}', [VoucherController::class, 'show'])->middleware('check.role:ReadVoucher');
            Route::put('/{voucher}/cancel', [VoucherController::class, 'cancel'])->middleware('check.role:CancelVoucher');
            Route::post('/{voucher}/edit', [VoucherController::class, 'edit'])->middleware('check.role:EditVoucher');
        }
    );
    # # # # # # # # # # # # # # # # # End Voucher  # # # # # # # # # # # # # # # 



    # # # # # # # # # # # # # # # # #  Transaction  # # # # # # # # # # # # # # # # #
    Route::controller(TransactionController::class)->prefix('transaction')->group(
        function () {
            Route::get('/', [TransactionController::class, 'index'])->middleware('check.role:ReadTransaction');
            Route::get('/new', [TransactionController::class, 'new'])->middleware('check.role:CreateTransaction');
            Route::post('/', [TransactionController::class, 'store'])->middleware('check.role:CreateTransaction');

            Route::get('/{company}', [TransactionController::class, 'show'])->middleware('check.role:ReadTransaction');
        }
    );
    # # # # # # # # # # # # # # # # # End Transaction  # # # # # # # # # # # # # # # 



        # # # # # # # # # # # # # # # # #  Sale  # # # # # # # # # # # # # # # # #
        Route::controller(SaleController::class)->prefix('sale')->group(
            function () {
                Route::get('/', [SaleController::class, 'index'])->middleware('check.role:ReadSale');
                Route::get('/new', [SaleController::class, 'new'])->middleware('check.role:CreateSale');
                Route::post('/', [SaleController::class, 'store'])->middleware('check.role:CreateSale');
    
                Route::get('/{company}', [SaleController::class, 'show'])->middleware('check.role:ReadSale');
                Route::post('/{voucher}/edit', [SaleController::class, 'edit'])->middleware('check.role:EditSale');
            }
        );
        # # # # # # # # # # # # # # # # # End Sale  # # # # # # # # # # # # # # # 
    




    # # # # # # # # # # # # # # # # #  Tower  # # # # # # # # # # # # # # # # #
    Route::controller(TowerController::class)->prefix('tower')->group(
        function () {
            Route::get('/', [TowerController::class, 'index'])->middleware('check.role:ReadTower');
            Route::get('/new', [TowerController::class, 'new'])->middleware('check.role:CreateTower');
            Route::post('/', [TowerController::class, 'store'])->middleware('check.role:CreateTower');

            Route::get('/{tower}', [TowerController::class, 'show'])->middleware('check.role:ReadTower');
            Route::delete('/{tower}', [TowerController::class, 'delete'])->middleware('check.role:DeleteTower');
            Route::get('/{tower}/edit', [TowerController::class, 'showForEdit'])->middleware('check.role:EditTower');
            Route::post('/{tower}/edit', [TowerController::class, 'edit'])->middleware('check.role:EditTower');
        }
    );
    # # # # # # # # # # # # # # # # # End Tower  # # # # # # # # # # # # # # # 





    # # # # # # # # # # # # # # # # #  Profile  # # # # # # # # # # # # # # # # #
    Route::controller(ProfileController::class)->prefix('profile')->group(
        function () {
            Route::get('/', [ProfileController::class, 'index'])->middleware('check.role:ReadProfile');
            Route::get('/new', [TowerController::class, 'new'])->middleware('check.role:CreateTower');
            Route::post('/', [TowerController::class, 'store'])->middleware('check.role:CreateTower');

            Route::get('/{tower}', [TowerController::class, 'show'])->middleware('check.role:ReadTower');
            Route::delete('/{tower}', [TowerController::class, 'delete'])->middleware('check.role:DeleteTower');
            Route::get('/{tower}/edit', [TowerController::class, 'showForEdit'])->middleware('check.role:EditTower');
            Route::post('/{tower}/edit', [TowerController::class, 'edit'])->middleware('check.role:EditTower');
        }
    );
    # # # # # # # # # # # # # # # # # End Profile  # # # # # # # # # # # # # # # 




    # # # # # # # # # # # # # # # # #  Join  # # # # # # # # # # # # # # # # #
    Route::controller(JoinController::class)->prefix('join')->group(
        function () {
            Route::get('/', [JoinController::class, 'index'])->middleware('check.role:ReadProfile');
            Route::get('/new', [TowerController::class, 'new'])->middleware('check.role:CreateTower');
            Route::post('/', [TowerController::class, 'store'])->middleware('check.role:CreateTower');

            Route::get('/{tower}', [TowerController::class, 'show'])->middleware('check.role:ReadTower');
            Route::delete('/{tower}', [TowerController::class, 'delete'])->middleware('check.role:DeleteTower');
            Route::get('/{tower}/edit', [TowerController::class, 'showForEdit'])->middleware('check.role:EditTower');
            Route::post('/{tower}/edit', [TowerController::class, 'edit'])->middleware('check.role:EditTower');
        }
    );
    # # # # # # # # # # # # # # # # # End Join  # # # # # # # # # # # # # # # 






    # # # # # # # # # # # # # # # # #  Banner  # # # # # # # # # # # # # # # # #
    Route::controller(BannerController::class)->prefix('banner')->group(
        function () {
            Route::get('/', [BannerController::class, 'index'])->middleware('check.role:ReadBanner');
            Route::get('/new', [BannerController::class, 'new'])->middleware('check.role:CreateBanner');
            Route::post('/', [BannerController::class, 'store'])->middleware('check.role:CreateBanner');

            Route::get('/{banner}', [BannerController::class, 'show'])->middleware('check.role:ReadBanner');
            Route::delete('/{banner}', [BannerController::class, 'delete'])->middleware('check.role:DeleteBanner');
            Route::get('/{banner}/edit', [BannerController::class, 'showForEdit'])->middleware('check.role:EditBanner');
            Route::post('/{banner}/edit', [BannerController::class, 'edit'])->middleware('check.role:EditBanner');
        }
    );
    # # # # # # # # # # # # # # # # # End Banner  # # # # # # # # # # # # # # # 

});
