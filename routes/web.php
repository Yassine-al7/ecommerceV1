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

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestion des produits (CRUD complet)
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::get('/products/{product}/assign', [AdminProductController::class, 'assignForm'])->name('products.assign');
    Route::post('/products/{product}/assign', [AdminProductController::class, 'assignStore'])->name('products.assign.store');
    Route::get('/products/{product}/assign', [AdminProductController::class, 'assignForm'])->name('products.assign');
    Route::post('/products/{product}/assign', [AdminProductController::class, 'assignStore'])->name('products.assign.store');

    // Gestion des vendeurs
    Route::get('/sellers', [AdminDashboardController::class, 'sellers'])->name('sellers.index');

    // Statistiques
    Route::get('/statistics', [AdminDashboardController::class, 'statistics'])->name('statistics.index');

    // Gestion du stock
    Route::get('/stock', [AdminDashboardController::class, 'stock'])->name('stock.index');

    // Commandes (Admin)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [AdminOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Facturation (Admin)
    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::patch('/invoices/{order}/status', [AdminInvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
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
Route::get('register/verify', [RegisterController::class, 'showVerifyCodeForm'])->name('register.verify.form');
Route::post('register/verify', [RegisterController::class, 'verifyCode'])->name('register.verify.submit');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Email Verification
Route::get('email/verify', [EmailVerificationController::class, 'notice'])->middleware('auth')->name('verification.notice');
Route::post('email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
Route::get('email/verify/{encodedEmail}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Seller Routes (require verified email)
Route::middleware(['auth', 'verified'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', function () {
        return view('seller.dashboard');
    })->name('dashboard');

    // Product routes
    Route::resource('products', SellerProductController::class);

    // Order routes for sellers
    Route::get('/orders', [\App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [\App\Http\Controllers\Seller\OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [\App\Http\Controllers\Seller\OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [\App\Http\Controllers\Seller\OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/edit', [\App\Http\Controllers\Seller\OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{id}', [\App\Http\Controllers\Seller\OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [\App\Http\Controllers\Seller\OrderController::class, 'destroy'])->name('orders.destroy');
    Route::patch('/orders/{id}/status', [\App\Http\Controllers\Seller\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

Route::get('register/seller', [RegisterController::class, 'showSellerRegistrationForm'])->name('register.seller');
Route::post('register/seller', [RegisterController::class, 'registerSeller']);

// Home redirect
Route::middleware('auth')->get('/home', function () {
    return redirect()->route('seller.dashboard');
})->name('home');
