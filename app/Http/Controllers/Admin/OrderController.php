<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\GeneratesOrderReferences;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use GeneratesOrderReferences;

    public function create()
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        $products = \App\Models\Product::all();
        return view('admin.order_form', compact('sellers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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

        // Générer automatiquement une référence unique
        $data['reference'] = $this->generateUniqueOrderReference();

        // Calcul des prix
        $product = \App\Models\Product::find($data['product_id']);
        $data['prix_produit'] = $product->prix_vente;
        $data['prix_commande'] = $product->prix_vente * $data['quantite_produit'];
        $data['produits'] = json_encode([['product_id' => $data['product_id'], 'qty' => $data['quantite_produit']]]);
        $data['status'] = 'en attente';

        $order = Order::create($data);

        return redirect()->route('admin.orders.index')->with('success', "Commande créée avec succès! Référence: {$order->reference}");
    }

    public function edit(Order $order)
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        $products = \App\Models\Product::all();

        // Décoder les produits de la commande existante
        $orderProducts = json_decode($order->produits, true) ?: [];

        return view('admin.order_form', compact('order', 'sellers', 'products', 'orderProducts'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'nom_client' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'adresse_client' => 'required|string',
            'numero_telephone_client' => 'required|string|max:20',
            'seller_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:produits,id',
            'products.*.taille_produit' => 'required|string',
            'products.*.quantite_produit' => 'required|integer|min:1',
            'commentaire' => 'nullable|string',
        ]);

        // Traiter chaque produit
        $produits = [];
        $prixTotalCommande = 0;

        foreach ($data['products'] as $productData) {
            $product = \App\Models\Product::find($productData['product_id']);
            $prixProduit = $product->prix_vente * (int) $productData['quantite_produit'];

            $prixTotalCommande += $prixProduit;

            $produits[] = [
                'product_id' => $productData['product_id'],
                'qty' => (int) $productData['quantite_produit'],
                'taille' => $productData['taille_produit'],
                'prix_vente_client' => $product->prix_vente,
            ];
        }

        // Mettre à jour la commande
        $order->update([
            'nom_client' => $data['nom_client'],
            'ville' => $data['ville'],
            'adresse_client' => $data['adresse_client'],
            'numero_telephone_client' => $data['numero_telephone_client'],
            'seller_id' => $data['seller_id'],
            'produits' => json_encode($produits),
            'prix_commande' => $prixTotalCommande,
            'commentaire' => $data['commentaire'] ?? null,
        ]);

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
            'status' => 'required|in:livré,retourné,pas de réponse,en attente,en livraison,refusé confirmé,non confirmé'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès!');
    }
}


