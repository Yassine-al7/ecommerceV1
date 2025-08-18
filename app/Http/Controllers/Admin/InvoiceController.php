<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer le vendeur sélectionné
        $selectedSellerId = $request->get('seller_id');

        // Construire la requête de base
        $query = Order::with(['seller'])
            ->where('status', 'livré');

        // Filtrer par vendeur si spécifié
        if ($selectedSellerId) {
            $query->where('seller_id', $selectedSellerId);
        }

        // Récupérer les commandes avec pagination
        $orders = $query->latest()->paginate(20);

        // Calculer les statistiques globales
        $stats = $this->calculateInvoiceStats($selectedSellerId);

        // Récupérer la liste des vendeurs pour le filtre
        $sellers = User::where('role', 'seller')
            ->whereHas('deliveredOrders')
            ->get();

        return view('admin.invoices', compact('orders', 'stats', 'sellers', 'selectedSellerId'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'facturation_status' => 'required|in:payé,non payé',
        ]);

        $order->facturation_status = $request->input('facturation_status');
        $order->save();

        return back()->with('success', 'Statut de facturation mis à jour avec succès.');
    }

    /**
     * Calculer les statistiques des factures
     */
    private function calculateInvoiceStats($sellerId = null)
    {
        $query = DB::table('commandes')->where('status', 'livré');

        if ($sellerId) {
            $query->where('seller_id', $sellerId);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_orders,
            COUNT(DISTINCT seller_id) as total_sellers,
            SUM(prix_commande) as total_revenue,
            SUM(prix_produit) as total_cost,
            SUM(prix_commande - prix_produit) as total_profit,
            COUNT(CASE WHEN facturation_status = "payé" THEN 1 END) as paid_orders,
            COUNT(CASE WHEN facturation_status = "non payé" OR facturation_status IS NULL THEN 1 END) as unpaid_orders
        ')->first();

        return $stats;
    }

    /**
     * Obtenir les statistiques par vendeur
     */
    public function sellerStats()
    {
        $sellerStats = DB::table('commandes')
            ->join('users', 'commandes.seller_id', '=', 'users.id')
            ->where('commandes.status', 'livré')
            ->selectRaw('
                users.id,
                users.name,
                COUNT(*) as total_orders,
                SUM(prix_commande) as total_revenue,
                SUM(prix_produit) as total_cost,
                SUM(prix_commande - prix_produit) as total_profit,
                COUNT(CASE WHEN facturation_status = "payé" THEN 1 END) as paid_orders,
                COUNT(CASE WHEN facturation_status = "non payé" OR facturation_status IS NULL THEN 1 END) as unpaid_orders
            ')
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return response()->json($sellerStats);
    }

    /**
     * Obtenir les données filtrées par vendeur (AJAX)
     */
    public function getFilteredData(Request $request)
    {
        $sellerId = $request->get('seller_id');
        $paymentStatus = $request->get('payment_status');
        $searchTerm = $request->get('search');

        $query = Order::with(['seller'])
            ->where('status', 'livré');

        // Filtre par vendeur
        if ($sellerId) {
            $query->where('seller_id', $sellerId);
        }

        // Filtre par statut de paiement
        if ($paymentStatus) {
            if ($paymentStatus === 'non payé') {
                $query->where(function($q) {
                    $q->where('facturation_status', 'non payé')
                      ->orWhereNull('facturation_status');
                });
            } else {
                $query->where('facturation_status', $paymentStatus);
            }
        }

        // Filtre par recherche
        if ($searchTerm) {
            $query->where('nom_client', 'like', "%{$searchTerm}%");
        }

        $orders = $query->latest()->get();

        // Calculer les totaux
        $totals = [
            'count' => $orders->count(),
            'revenue' => $orders->sum('prix_commande'),
            'cost' => $orders->sum('prix_produit'),
            'profit' => $orders->sum('prix_commande') - $orders->sum('prix_produit')
        ];

        return response()->json([
            'orders' => $orders,
            'totals' => $totals
        ]);
    }

    /**
     * Marquer toutes les commandes d'un vendeur comme payées
     */
    public function markAllAsPaid(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
        ]);

        $updated = Order::where('seller_id', $request->seller_id)
            ->where('status', 'livré')
            ->where(function($query) {
                $query->where('facturation_status', 'non payé')
                      ->orWhereNull('facturation_status');
            })
            ->update(['facturation_status' => 'payé']);

        return back()->with('success', "{$updated} commande(s) marquée(s) comme payée(s) pour ce vendeur.");
    }

    /**
     * Exporter les données de facturation
     */
    public function export(Request $request)
    {
        $sellerId = $request->get('seller_id');
        $paymentStatus = $request->get('payment_status');

        $query = Order::with(['seller'])
            ->where('status', 'livré');

        // Appliquer les filtres
        if ($sellerId) {
            $query->where('seller_id', $sellerId);
        }

        if ($paymentStatus) {
            if ($paymentStatus === 'non payé') {
                $query->where(function($q) {
                    $q->where('facturation_status', 'non payé')
                      ->orWhereNull('facturation_status');
                });
            } else {
                $query->where('facturation_status', $paymentStatus);
            }
        }

        $orders = $query->get();

        $filename = 'factures_' . date('Y-m-d_H-i-s');
        if ($sellerId) {
            $seller = User::find($sellerId);
            $filename .= '_' . str_replace(' ', '_', $seller->name);
        }
        $filename .= '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID Commande',
                'Référence',
                'Vendeur',
                'Client',
                'Ville',
                'Produits',
                'Prix Vente (MAD)',
                'Prix Produit (MAD)',
                'Bénéfice (MAD)',
                'Statut Paiement',
                'Date Livraison'
            ]);

            // Données
            foreach ($orders as $order) {
                $produits = is_array($order->produits) ? implode(', ', $order->produits) : $order->produits;
                $benefice = $order->prix_commande - $order->prix_produit;

                fputcsv($file, [
                    $order->id,
                    $order->reference,
                    $order->seller->name ?? 'N/A',
                    $order->nom_client,
                    $order->ville,
                    $produits,
                    $order->prix_commande,
                    $order->prix_produit,
                    $benefice,
                    $order->facturation_status ?? 'non payé',
                    $order->updated_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}


