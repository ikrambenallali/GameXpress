<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\dashboardController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return 'Hello World';
});
Route::prefix('v1/admin')->group(function () {
    Route::post('/register', [authController::class, 'register']);
    Route::post('/login', [authController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware(['role:super_admin', 'auth:sanctum'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return 'Yes, you can see this page';
    });
    // 
});
Route::middleware(['role:super_admin', 'auth:sanctum'])->group(function () {
    Route::prefix('v1/admin')->group(function () {
        Route::get('/dashboard', [dashboardController::class, 'statistique']);
        Route::get('AllUsers',[UserController::class,'index'])->name('users.index');
        Route::post('users',[UserController::class,'store'])->name('users.store');


    });
});
Route::middleware(['role:product_manager|super_admin', 'auth:sanctum'])->group(function () {
    Route::prefix('v1/admin')->group(function () {
        Route::post('/product', [ProductController::class, 'store'])->name('products.store');
        Route::put('/product/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/Allproduct', [ProductController::class, 'index'])->name('product.index');
        Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
        Route::put('/category/{category}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
        Route::get('/Allcategory', [CategoryController::class, 'index'])->name('category.index');
    });
});
Route::middleware(['role:user_manager|super_admin'])->group(function () {
    Route::prefix('v1/admin')->group(function () {
        Route::get('AllUsers',[UserController::class,'index'])->name('users.index');
        Route::post('users',[UserController::class,'store'])->name('users.store');
        Route::put('users',[UserController::class,'update'])->name('users.update');
        Route::delete('users',[UserController::class,'destroy'])->name('users.destroy');
        
        

    });
});
