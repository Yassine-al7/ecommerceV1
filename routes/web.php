<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});


// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Email Verification
Route::get('email/verify/{encodedEmail}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Seller Routes
Route::middleware('auth')->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', function () {
        return view('seller.dashboard');
    })->name('dashboard');

    // Product routes
    Route::resource('products', SellerProductController::class);
});

Route::get('register/seller', [RegisterController::class, 'showSellerRegistrationForm'])->name('register.seller');
Route::post('register/seller', [RegisterController::class, 'registerSeller']);

// Home redirect
Route::middleware('auth')->get('/home', function () {
    return redirect()->route('seller.dashboard');
})->name('home');
