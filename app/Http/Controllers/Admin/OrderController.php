<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\GeneratesOrderReferences;
use App\Services\StockService;
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

        // Vérifier le stock disponible
        if ($product->quantite_stock < $data['quantite_produit']) {
            return back()->withErrors(['quantite_produit' => 'Stock insuffisant. Stock disponible: ' . $product->quantite_stock]);
        }

        // Créer la commande
        $order = Order::create($data);

        // Diminuer automatiquement le stock du produit
        $success = StockService::decreaseStock(
            $data['product_id'],
            'Couleur unique', // Couleur par défaut pour les commandes admin
            $data['quantite_produit']
        );

        if (!$success) {
            Log::error("Échec de la mise à jour du stock pour le produit ID: {$data['product_id']}");
        }

        // Log de la diminution du stock
        $product = \App\Models\Product::find($data['product_id']);
        if ($product) {
            Log::info("Stock diminué pour le produit {$product->name} (ID: {$product->id}) - Quantité: {$data['quantite_produit']} - Nouveau stock: {$product->quantite_stock}");
        }

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

        // Récupérer les anciennes quantités pour ajuster le stock
        $oldProducts = json_decode($order->produits, true) ?: [];
        $oldQuantities = [];
        foreach ($oldProducts as $oldProduct) {
            $oldQuantities[$oldProduct['product_id']] = $oldProduct['qty'];
        }

        // Traiter chaque produit et vérifier le stock
        $produits = [];
        $prixTotalCommande = 0;

        foreach ($data['products'] as $productData) {
            $product = \App\Models\Product::find($productData['product_id']);
            $newQuantity = (int) $productData['quantite_produit'];
            $oldQuantity = $oldQuantities[$productData['product_id']] ?? 0;

            // Calculer la différence de quantité
            $quantityDifference = $newQuantity - $oldQuantity;

            // Vérifier si on peut augmenter le stock (si la nouvelle quantité est plus grande)
            if ($quantityDifference > 0) {
                if ($product->quantite_stock < $quantityDifference) {
                    return back()->withErrors(['quantite_produit' => "Stock insuffisant pour {$product->name}. Stock disponible: {$product->quantite_stock}"]);
                }
            }

            $prixProduit = $product->prix_vente * $newQuantity;
            $prixTotalCommande += $prixProduit;

            $produits[] = [
                'product_id' => $productData['product_id'],
                'qty' => $newQuantity,
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

        // Ajuster le stock pour chaque produit
        foreach ($data['products'] as $productData) {
            $product = \App\Models\Product::find($productData['product_id']);
            $newQuantity = (int) $productData['quantite_produit'];
            $oldQuantity = $oldQuantities[$productData['product_id']] ?? 0;

            if ($newQuantity !== $oldQuantity) {
                $success = StockService::adjustStock(
                    $productData['product_id'],
                    'Couleur unique', // Couleur par défaut pour les commandes admin
                    $oldQuantity,
                    $newQuantity
                );

                if (!$success) {
                    Log::error("Échec de l'ajustement du stock pour le produit ID: {$productData['product_id']}");
                } else {
                    Log::info("Stock ajusté pour {$product->name} (ID: {$product->id}) - Ancienne quantité: {$oldQuantity}, Nouvelle quantité: {$newQuantity}");
                }
            }
        }

        return redirect()->route('admin.orders.index')->with('success', 'Commande mise à jour avec succès!');
    }

    public function destroy(Order $order)
    {
        // Récupérer les produits de la commande pour remettre le stock
        $products = json_decode($order->produits, true) ?: [];

        foreach ($products as $productData) {
            $success = StockService::increaseStock(
                $productData['product_id'],
                'Couleur unique', // Couleur par défaut pour les commandes admin
                $productData['qty']
            );

            if (!$success) {
                Log::error("Échec de la remise du stock pour le produit ID: {$productData['product_id']}");
            } else {
                $product = \App\Models\Product::find($productData['product_id']);
                if ($product) {
                    Log::info("Stock remis pour {$product->name} (ID: {$product->id}) - Quantité: {$productData['qty']} - Nouveau stock: {$product->quantite_stock}");
                }
            }
        }

        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Commande supprimée avec succès! Le stock a été remis.');
    }
    public function index()
    {
        $orders = Order::with('seller')->latest()->paginate(20);

        // Calculer les statistiques
        $allOrders = Order::all(); // Pour les statistiques complètes
        $stats = \App\Helpers\OrderHelper::calculateOrderStats($allOrders);

        return view('admin.orders', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        return view('admin.order_show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:en attente,non confirmé,confirme,en livraison,livre,pas de réponse,annulé,retourné'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès!');
    }

    /**
     * Supprimer plusieurs commandes en lot
     */
    public function bulkDelete(Request $request)
    {
        // Debug: logger la requête reçue
        \Log::info('Bulk delete request reçue:', [
            'all_data' => $request->all(),
            'order_ids' => $request->order_ids,
            'method' => $request->method(),
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'order_ids' => 'required|array|min:1',
                'order_ids.*' => 'required|integer|exists:commandes,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation bulk delete:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation: ' . implode(', ', array_flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        }

        $orderIds = $request->order_ids;
        $deletedCount = 0;
        $errors = [];

        foreach ($orderIds as $orderId) {
            try {
                $order = Order::findOrFail($orderId);

                // Récupérer les produits de la commande pour remettre le stock
                $products = json_decode($order->produits, true) ?: [];

                foreach ($products as $productData) {
                    $product = \App\Models\Product::find($productData['product_id']);
                    if ($product) {
                        // Remettre le stock qui avait été diminué
                        $product->increment('quantite_stock', $productData['qty']);

                        \Log::info("Stock remis en lot pour {$product->name} (ID: {$product->id}) - Quantité: {$productData['qty']} - Nouveau stock: {$product->quantite_stock}");
                    }
                }

                $order->delete();
                $deletedCount++;

                \Log::info("Commande supprimée en lot - ID: {$orderId}, Référence: {$order->reference}");

            } catch (\Exception $e) {
                $errors[] = "Erreur lors de la suppression de la commande ID {$orderId}: " . $e->getMessage();
                \Log::error("Erreur suppression en lot commande ID {$orderId}: " . $e->getMessage());
            }
        }

        if ($deletedCount > 0) {
            $message = "{$deletedCount} commande(s) supprimée(s) avec succès! Le stock a été remis.";

            if (!empty($errors)) {
                $message .= " Erreurs: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'errors' => $errors
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Aucune commande n\'a pu être supprimée.',
                'errors' => $errors
            ], 400);
        }
    }
}


