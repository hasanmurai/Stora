<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController, ShopController, ProductController,
                                 AdminController, PublicController};


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/shops/{shopId}/products', [ProductController::class, 'listProducts']);
Route::get('/search', [PublicController::class, 'search']);
Route::get('/shops/{slug}', [PublicController::class, 'showShop']);
Route::get('/products/{slug}', [PublicController::class, 'showProduct']);

Route::middleware('auth:api','check.banned')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::prefix('profile')->controller(AuthController::class)->group(function () {
        Route::get('/','getProfile');
        Route::put('/', 'editProfile');
        Route::put('/password', 'changePassword');
    });

    Route::prefix('shops')->controller(ShopController::class)->group(function () {
        Route::get('/', 'listShops');
        Route::post('/', 'addShop');
        Route::patch('/{id}', 'editShop');
        Route::delete('/{id}', 'deleteShop');
    });

    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::post('/{shopId}', 'addProduct'); 
        Route::put('/{id}', 'editProduct');
        Route::delete('/{id}', 'deleteProduct');
    });
});

Route::middleware(['auth:api', 'admin', 'check.banned'])->group(function () {
    Route::prefix('admin')->controller(AdminController::class)->group(function () {
        Route::patch('/users/{id}/role', 'assignRole');
        Route::get('/dashboard', 'stats');
        Route::get('/users', 'listAllUsers');
        Route::get('/search','search');
        Route::patch('/users/{id}/status', 'toggleStatus');
    });
});
