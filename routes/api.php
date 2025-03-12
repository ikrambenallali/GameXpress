<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\dashboardController;

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
        Route::post('/category', [CategoryController::class, 'store']);
        Route::put('/category/{category}', [CategoryController::class, 'update']);
        Route::delete('/category/{category}', [CategoryController::class, 'destroy']);
    });
});
