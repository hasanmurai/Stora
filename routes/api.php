<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ProductController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::get('/shops/{id}', [ShopController::class, 'getShop']);
Route::get('/products/{shopId}', [ProductController::class, 'listProducts']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update', [AuthController::class, 'editProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    Route::prefix('shops')->group(function () {
        Route::post('/add', [ShopController::class, 'addShop']);
        Route::get('/list', [ShopController::class, 'listShops']);
        Route::delete('/{id}', [ShopController::class, 'deleteShop']);
        Route::put('/{id}', [ShopController::class, 'editShop']);
    });

    Route::prefix('products')->group(function () {
        Route::post('/add/{shopId}', [ProductController::class, 'addProduct']);
        Route::put('/{productId}', [ProductController::class, 'editProduct']);
        Route::delete('/{productId}', [ProductController::class, 'deleteProduct']);
    });
});
