<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        // Only delivered orders for invoicing
        $orders = Order::where('status', 'livré')->latest()->paginate(20);
        return view('admin.invoices', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'facturation_status' => 'required|in:payé,non payé',
        ]);

        $order->facturation_status = $request->input('facturation_status');
        $order->save();

        return back()->with('success', 'Statut de facturation mis à jour.');
    }
}


