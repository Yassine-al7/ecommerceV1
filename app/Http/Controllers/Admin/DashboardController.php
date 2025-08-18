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
        $totalSellers = User::where('role_id', 2)->count(); // Assuming role_id 2 is for sellers

        return view('admin.dashboard', compact('totalOrders', 'totalSellers'));
    }

    public function statistics()
    {
        // Logic for generating statistics can be added here
        return view('admin.statistics');
    }
}
