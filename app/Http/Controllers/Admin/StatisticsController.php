<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalSellers = User::where('role_id', 2)->count(); // Assuming role_id 2 is for sellers
        $bestSeller = User::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->first();
        $productsSold = Product::withCount('orderItems')
            ->get()
            ->map(function ($product) {
                return [
                    'product' => $product,
                    'units_sold' => $product->order_items_count,
                ];
            });

        return view('admin.statistics', compact('totalOrders', 'totalSellers', 'bestSeller', 'productsSold'));
    }
}
