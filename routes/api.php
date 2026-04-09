<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AuthController, ShopController, ProductController, AdminController};


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/shops/{shopId}/products', [ProductController::class, 'listProducts']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::prefix('profile')->controller(AuthController::class)->group(function () {
        Route::put('/', 'editProfile');
        Route::put('/password', 'changePassword');
    });

    Route::prefix('shops')->controller(ShopController::class)->group(function () {
        Route::get('/', 'listShops');
        Route::post('/', 'addShop');
        Route::put('/{id}', 'editShop');
        Route::delete('/{id}', 'deleteShop');
    });

    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::post('/{shopId}', 'addProduct'); 
        Route::put('/{id}', 'editProduct');
        Route::delete('/{id}', 'deleteProduct');
    });
});

Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::patch('/users/{id}', [AdminController::class, 'assignRole']);
    Route::get('/dashboard', [AdminController::class, 'stats']);
    Route::get('/users', [AdminController::class, 'listAllUsers']);
    Route::get('/search',[AdminController::class, 'search']);
});
