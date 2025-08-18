<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/statistics', [App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/sellers', [App\Http\Controllers\Admin\SellerController::class, 'index'])->name('sellers.index');
    Route::get('/stock', [App\Http\Controllers\Admin\StatisticsController::class, 'stock'])->name('stock.index');

    // Admin Management
    Route::resource('admins', App\Http\Controllers\Admin\AdminController::class)->except(['show', 'edit', 'update']);

    // Products CRUD
    Route::resource('products', AdminProductController::class);
    Route::get('/products/{product}/assign', [AdminProductController::class, 'assignForm'])->name('products.assign');
    Route::post('/products/{product}/assign', [AdminProductController::class, 'assignStore'])->name('products.assign.store');

    // Orders CRUD
    Route::resource('orders', AdminOrderController::class);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Invoices
    Route::get('/invoices', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
    Route::patch('/invoices/{order}/status', [App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
});


// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Verification code routes (accessible to both guest and authenticated users)
Route::get('/register/verify', [App\Http\Controllers\Auth\RegisterController::class, 'showVerifyCodeForm'])->name('register.verify');
Route::post('/register/verify', [App\Http\Controllers\Auth\RegisterController::class, 'verifyCode'])->name('register.verify.code');
Route::post('/register/resend-code', [App\Http\Controllers\Auth\RegisterController::class, 'resendCode'])->name('register.resend-code');

// CSRF token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

// Logout
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Seller Routes (require verified email)
Route::middleware(['auth', 'verified'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Seller\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [App\Http\Controllers\Seller\ProductController::class, 'index'])->name('products.index');
    Route::resource('orders', App\Http\Controllers\Seller\OrderController::class);
});

// Home redirect
Route::middleware('auth')->get('/home', function () {
    return redirect()->route('seller.dashboard');
})->name('home');
