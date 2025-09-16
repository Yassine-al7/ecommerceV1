<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer le vendeur sélectionné
        $selectedSellerId = $request->get('seller_id');
        $groupBy = $request->get('group_by', 'none'); // none, seller, payment_status

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

        // Calculer les totaux par vendeur pour le groupement
        $sellerTotals = $this->calculateSellerTotals($selectedSellerId);

        return view('admin.invoices', compact('orders', 'stats', 'sellers', 'selectedSellerId', 'groupBy', 'sellerTotals'));
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
     * Calculer les totaux par vendeur
     */
    private function calculateSellerTotals($sellerId = null)
    {
        $query = DB::table('commandes')
            ->join('users', 'commandes.seller_id', '=', 'users.id')
            ->where('commandes.status', 'livré')
            ->where('users.role', 'seller');

        if ($sellerId) {
            $query->where('commandes.seller_id', $sellerId);
        }

        $sellerTotals = $query
            ->select([
                'users.id as seller_id',
                'users.name as seller_name',
                'users.rib as seller_rib',
                DB::raw('COUNT(commandes.id) as total_orders'),
                DB::raw('SUM(commandes.prix_commande) as total_revenue'),
                DB::raw('SUM(commandes.marge_benefice) as total_profit'),
                DB::raw('SUM(CASE WHEN commandes.facturation_status = "payé" THEN commandes.marge_benefice ELSE 0 END) as paid_profit'),
                DB::raw('SUM(CASE WHEN commandes.facturation_status != "payé" OR commandes.facturation_status IS NULL THEN commandes.marge_benefice ELSE 0 END) as unpaid_profit')
            ])
            ->groupBy('users.id', 'users.name', 'users.rib')
            ->orderBy('total_profit', 'desc')
            ->get();

        return $sellerTotals;
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
            SUM(CASE WHEN facturation_status = "payé" THEN 1 ELSE 0 END) as paid_orders,
            SUM(CASE WHEN facturation_status = "non payé" OR facturation_status IS NULL THEN 1 ELSE 0 END) as unpaid_orders,
            SUM(CASE WHEN facturation_status = "non payé" OR facturation_status IS NULL THEN marge_benefice ELSE 0 END) as total_marge_benefice
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
                users.rib,
                COUNT(*) as total_orders,
                SUM(prix_commande) as total_revenue,
                COUNT(CASE WHEN facturation_status = "payé" THEN 1 END) as paid_orders,
                COUNT(CASE WHEN facturation_status = "non payé" OR facturation_status IS NULL THEN 1 END) as unpaid_orders
            ')
            ->groupBy('users.id', 'users.name', 'users.rib')
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

        // Calculer les totaux - marge bénéfice seulement pour les factures non payées
        $totalRevenue = $orders->sum('prix_commande');
        $totalMargeBenefice = $orders->whereIn('facturation_status', ['non payé', null])->sum('marge_benefice');

        $totals = [
            'count' => $orders->count(),
            'revenue' => $totalRevenue,
            'marge_benefice' => $totalMargeBenefice
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

            // En-têtes CSV avec BOM UTF-8 pour Excel
            fwrite($file, "\xEF\xBB\xBF");

            // En-têtes CSV
            fputcsv($file, [
                'ID Commande',
                'Reference',
                'Vendeur',
                'Client',
                'Ville',
                'Produits',
                'Prix Commande (MAD)',
                'Cout Vendeur (MAD)',
                'Benefice Vendeur (MAD)',
                'Statut Paiement',
                'Date Livraison'
            ], ';'); // Utiliser ; au lieu de , pour Excel

            // Données
            foreach ($orders as $order) {
                // Décoder les produits JSON
                $produits = '';
                if (is_string($order->produits)) {
                    $decodedProducts = json_decode($order->produits, true);
                    if (is_array($decodedProducts)) {
                        $productNames = [];
                        foreach ($decodedProducts as $produit) {
                            if (isset($produit['product_id']) && isset($produit['qty'])) {
                                $product = \App\Models\Product::find($produit['product_id']);
                                $productName = $product ? $product->name : 'Produit ID: ' . $produit['product_id'];
                                $productNames[] = $productName . ' (x' . $produit['qty'] . ')';
                            }
                        }
                        $produits = implode(' | ', $productNames);
                    }
                } elseif (is_array($order->produits)) {
                    $produits = implode(' | ', $order->produits);
                } else {
                    $produits = $order->produits;
                }

                // Calculer le coût vendeur et bénéfice
                $coutVendeur = 0;
                if (is_string($order->produits)) {
                    $decodedProducts = json_decode($order->produits, true);
                    if (is_array($decodedProducts)) {
                        foreach ($decodedProducts as $produit) {
                            if (isset($produit['product_id']) && isset($produit['qty'])) {
                                $productUser = \DB::table('product_user')
                                    ->where('product_id', $produit['product_id'])
                                    ->where('user_id', $order->seller_id)
                                    ->first();

                                if ($productUser) {
                                    $coutVendeur += ($productUser->prix_admin ?? 0) * $produit['qty'];
                                }
                            }
                        }
                    }
                }

                $beneficeVendeur = $order->prix_commande - $coutVendeur;

                fputcsv($file, [
                    $order->id,
                    $order->reference ?? 'N/A',
                    $order->seller->name ?? 'N/A',
                    $order->nom_client ?? 'N/A',
                    $order->ville ?? 'N/A',
                    $produits,
                    $order->prix_commande, // Nombre brut sans formatage
                    $coutVendeur, // Nombre brut sans formatage
                    $beneficeVendeur, // Nombre brut sans formatage
                    $order->facturation_status ?? 'non paye',
                    $order->updated_at->format('d/m/Y H:i')
                ], ';'); // Utiliser ; au lieu de , pour Excel
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Télécharger un PDF des commandes livrées non payées pour un vendeur
     */
    public function downloadUnpaidPdf(User $seller)
    {
        // Récupérer les commandes livrées non payées (ou null) pour ce vendeur
        $orders = Order::with('seller')
            ->where('seller_id', $seller->id)
            ->where('status', 'livré')
            ->where(function($q) {
                $q->where('facturation_status', 'non payé')
                  ->orWhereNull('facturation_status');
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        // Générer des totaux
        $totals = [
            'count' => $orders->count(),
            'revenue' => $orders->sum('prix_commande'),
            'marge_benefice' => $orders->sum('marge_benefice'),
        ];

        // Rendre la vue Blade en HTML
        $html = view('admin.pdf.unpaid_invoices', [
            'seller' => $seller,
            'orders' => $orders,
            'totals' => $totals,
            'generatedAt' => now(),
        ])->render();

        // Configurer mPDF (prise en charge RTL/Arabe)
        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0755, true);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'tempDir' => $tempDir,
            'default_font' => 'dejavusans',
        ]);
        $mpdf->autoLangToFont = true;
        $mpdf->SetDirectionality('rtl');

        $mpdf->WriteHTML($html);

        $filename = 'unpaid_invoices_' . str_replace(' ', '_', $seller->name) . '_' . now()->format('Ymd_His') . '.pdf';
        // Retourner la réponse en téléchargement
        return response($mpdf->Output($filename, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}


