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
});



// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::get('/products/{shopId}', [ProductController::class, 'listProducts']);

// Route::middleware('auth:api')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::put('/update', [AuthController::class, 'editProfile']);
//     Route::put('/change-password', [AuthController::class, 'changePassword']);

//     Route::prefix('shops')->group(function () {
//         Route::post('/add', [ShopController::class, 'addShop']);
//         Route::get('/list', [ShopController::class, 'listShops']);
//         Route::delete('/{id}', [ShopController::class, 'deleteShop']);
//         Route::put('/{id}', [ShopController::class, 'editShop']);
//     });

//     Route::prefix('products')->group(function () {
//         Route::post('/add/{shopId}', [ProductController::class, 'addProduct']);
//         Route::put('/{productId}', [ProductController::class, 'editProduct']);
//         Route::delete('/{productId}', [ProductController::class, 'deleteProduct']);
//     });
// });

// Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
//     // Route::get('/dashboard', [AdminController::class, 'stats']);
//     // Route::get('/all-users', [AdminController::class, 'listAllUsers']);
//     // Route::delete('/force-delete-shop/{id}', [AdminController::class, 'deleteAnyShop']);
// });