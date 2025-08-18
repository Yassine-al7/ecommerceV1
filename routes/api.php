<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SellerController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\OrderController;

// Authentication routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Admin routes
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('sellers', SellerController::class);
    Route::get('statistics', [StatisticsController::class, 'index']);
});

// Seller routes
Route::middleware(['auth:api', 'role:seller'])->group(function () {
    Route::apiResource('seller/products', SellerProductController::class);
    Route::get('seller/orders', [OrderController::class, 'index']);
    Route::get('seller/orders/{order}', [OrderController::class, 'show']);
    Route::put('seller/orders/{order}/update-status', [OrderController::class, 'updateStatus']);
});
