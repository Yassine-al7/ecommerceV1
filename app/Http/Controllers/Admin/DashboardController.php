<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalSellers = User::where('role', 'seller')->count(); // Utiliser le champ 'role' au lieu de 'role_id'

        return view('admin.dashboard', compact('totalOrders', 'totalSellers'));
    }

    public function products()
    {
        $products = \App\Models\Product::with('category')->get();
        return view('admin.products', compact('products'));
    }

    public function sellers()
    {
        $sellers = User::where('role', 'seller')->get();
        return view('admin.sellers', compact('sellers'));
    }

    public function statistics()
    {
        // Logic for generating statistics can be added here
        return view('admin.statistics');
    }

    public function stock()
    {
        $products = \App\Models\Product::all();
        return view('admin.stock', compact('products'));
    }
}
