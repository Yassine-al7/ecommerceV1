<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('seller_id', auth()->id())->get();
        return view('seller.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
        return view('seller.order_detail', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
        $order->status = $request->input('status');
        $order->save();

        return redirect()->route('seller.orders.index')->with('success', 'Order status updated successfully.');
    }
}
