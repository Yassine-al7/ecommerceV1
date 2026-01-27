<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\OrderController;

// Authentication routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('admin/products', AdminProductController::class)->names([
        'index' => 'api.admin.products.index',
        'store' => 'api.admin.products.store',
        'show' => 'api.admin.products.show',
        'update' => 'api.admin.products.update',
        'destroy' => 'api.admin.products.destroy'
    ]);

    Route::get('statistics', [StatisticsController::class, 'index']);
});

// Seller routes
Route::middleware(['auth:sanctum', 'seller'])->group(function () {
    Route::apiResource('seller/products', SellerProductController::class)->names([
        'index' => 'api.seller.products.index',
        'store' => 'api.seller.products.store',
        'show' => 'api.seller.products.show',
        'update' => 'api.seller.products.update',
        'destroy' => 'api.seller.products.destroy'
    ]);
    Route::get('seller/orders', [OrderController::class, 'index']);
    Route::get('seller/orders/{order}', [OrderController::class, 'show']);
    Route::put('seller/orders/{order}/update-status', [OrderController::class, 'updateStatus']);

    // Route pour récupérer les données de stock mises à jour
    Route::get('products/{product}/stock', function ($product) {
        $product = \App\Models\Product::find($product);
        if (!$product) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'quantite_stock' => $product->quantite_stock,
            'stock_couleurs' => $product->stock_couleurs,
            'couleur' => $product->couleur,
            'tailles' => $product->tailles
        ]);
    });
});
