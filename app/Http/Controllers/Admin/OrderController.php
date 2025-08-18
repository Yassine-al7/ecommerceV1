<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        return view('admin.order_form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reference' => 'required|string',
            'nom_client' => 'required|string',
            'ville' => 'required|string',
            'adresse_client' => 'required|string',
            'numero_telephone_client' => 'required|string',
            'produits' => 'required|array',
            'taille_produit' => 'required|string',
            'quantite_produit' => 'required|integer',
            'prix_produit' => 'required|numeric',
            'prix_commande' => 'required|numeric',
            'status' => 'required|string',
            'commentaire' => 'nullable|string',
            'seller_id' => 'nullable|exists:users,id',
        ]);

        $data['produits'] = json_encode($data['produits']);
        Order::create($data);
        return redirect()->route('admin.orders.index')->with('success', 'Commande créée.');
    }

    public function edit(Order $order)
    {
        return view('admin.order_form', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'reference' => 'required|string',
            'nom_client' => 'required|string',
            'ville' => 'required|string',
            'adresse_client' => 'required|string',
            'numero_telephone_client' => 'required|string',
            'produits' => 'required|array',
            'taille_produit' => 'required|string',
            'quantite_produit' => 'required|integer',
            'prix_produit' => 'required|numeric',
            'prix_commande' => 'required|numeric',
            'status' => 'required|string',
            'commentaire' => 'nullable|string',
            'seller_id' => 'nullable|exists:users,id',
        ]);

        $data['produits'] = json_encode($data['produits']);
        $order->update($data);
        return redirect()->route('admin.orders.index')->with('success', 'Commande mise à jour.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Commande supprimée.');
    }
    public function index()
    {
        $orders = Order::latest()->paginate(20);
        return view('admin.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.order_show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order->status = $request->input('status');
        $order->save();

        return back()->with('success', 'Le statut de la commande a été mis à jour.');
    }
}


