<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        $products = \App\Models\Product::all();
        return view('admin.order_form', compact('sellers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reference' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'adresse_client' => 'required|string',
            'numero_telephone_client' => 'required|string|max:20',
            'seller_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:produits,id',
            'taille_produit' => 'required|string|max:10',
            'quantite_produit' => 'required|integer|min:1',
            'commentaire' => 'nullable|string',
        ]);

        // Calcul des prix
        $product = \App\Models\Product::find($data['product_id']);
        $data['prix_produit'] = $product->prix_vente;
        $data['prix_commande'] = $product->prix_vente * $data['quantite_produit'];
        $data['produits'] = json_encode([['product_id' => $data['product_id'], 'qty' => $data['quantite_produit']]]);
        $data['status'] = 'en attente';

        Order::create($data);

        return redirect()->route('admin.orders.index')->with('success', 'Commande créée avec succès!');
    }

    public function edit(Order $order)
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        $products = \App\Models\Product::all();
        return view('admin.order_form', compact('order', 'sellers', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'reference' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'adresse_client' => 'required|string',
            'numero_telephone_client' => 'required|string|max:20',
            'seller_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:produits,id',
            'taille_produit' => 'required|string|max:10',
            'quantite_produit' => 'required|integer|min:1',
            'commentaire' => 'nullable|string',
        ]);

        // Calcul des prix
        $product = \App\Models\Product::find($data['product_id']);
        $data['prix_produit'] = $product->prix_vente;
        $data['prix_commande'] = $product->prix_vente * $data['quantite_produit'];
        $data['produits'] = json_encode([['product_id' => $data['product_id'], 'qty' => $data['quantite_produit']]]);

        $order->update($data);

        return redirect()->route('admin.orders.index')->with('success', 'Commande mise à jour avec succès!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Commande supprimée avec succès!');
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
            'status' => 'required|in:en attente,en cours,livré,annulé'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès!');
    }
}


