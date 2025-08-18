<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::where('seller_id', auth()->id());
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('q')) {
            $q = '%' . request('q') . '%';
            $query->where(function ($s) use ($q) {
                $s->where('reference', 'like', $q)
                  ->orWhere('nom_client', 'like', $q)
                  ->orWhere('ville', 'like', $q);
            });
        }
        $orders = $query->latest()->paginate(15);
        return view('seller.orders', compact('orders'));
    }

    public function create()
    {
        // Produits assignés au vendeur
        $products = auth()->user()->assignedProducts()->select('products.id','products.name')->get();
        return view('seller.order_form', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reference' => 'required|string',
            'nom_client' => 'required|string',
            'ville' => 'required|string',
            'adresse_client' => 'required|string',
            'numero_telephone_client' => 'required|string',
            'product_id' => 'required|exists:produits,id',
            'taille_produit' => 'required|string',
            'quantite_produit' => 'required|integer|min:1',
            'commentaire' => 'nullable|string',
        ]);
        // Calcul des prix à partir du pivot assigné
        $product = auth()->user()->assignedProducts()->where('products.id', $data['product_id'])->firstOrFail();
        $prixVente = (float) optional($product->pivot)->prix_vente;
        $data['prix_produit'] = $prixVente;
        $data['prix_commande'] = $prixVente * (int) $data['quantite_produit'];
        $data['produits'] = json_encode([['product_id' => $data['product_id'], 'qty' => (int) $data['quantite_produit']]]);
        $data['status'] = 'en attente';
        $data['seller_id'] = auth()->id();
        Order::create($data);

        return redirect()->route('seller.orders.index')->with('success', 'Commande créée (en attente).');
    }

    public function show($id)
    {
        $order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
        return view('seller.order_detail', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
        $products = auth()->user()->assignedProducts()->select('products.id','products.name')->get();
        return view('seller.order_form', compact('order','products'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

        $data = $request->validate([
            'reference' => 'required|string',
            'nom_client' => 'required|string',
            'ville' => 'required|string',
            'adresse_client' => 'required|string',
            'numero_telephone_client' => 'required|string',
            'product_id' => 'required|exists:produits,id',
            'taille_produit' => 'required|string',
            'quantite_produit' => 'required|integer|min:1',
            'commentaire' => 'nullable|string',
        ]);
        $product = auth()->user()->assignedProducts()->where('products.id', $data['product_id'])->firstOrFail();
        $prixVente = (float) optional($product->pivot)->prix_vente;
        $data['prix_produit'] = $prixVente;
        $data['prix_commande'] = $prixVente * (int) $data['quantite_produit'];
        $data['produits'] = json_encode([['product_id' => $data['product_id'], 'qty' => (int) $data['quantite_produit']]]);
        $order->update($data);

        return redirect()->route('seller.orders.index')->with('success', 'Commande mise à jour.');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
        $order->status = $request->input('status');
        $order->save();

        return redirect()->route('seller.orders.index')->with('success', 'Order status updated successfully.');
    }

    public function destroy($id)
    {
        $order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
        $order->delete();
        return redirect()->route('seller.orders.index')->with('success', 'Commande supprimée.');
    }
}
