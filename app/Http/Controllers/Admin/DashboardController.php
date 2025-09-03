<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Services\ColorStockNotificationService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer les filtres temporels
        $period = $request->get('period', 'all'); // all, today, week, month, year
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Construire la requête de base
        $query = Order::query();

        // Appliquer les filtres temporels
        if ($period !== 'all') {
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        } elseif ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        }

        // Statistiques des commandes
        $totalOrders = $query->count();

        // Chiffre d'affaires = Total des commandes livrées uniquement
        $totalRevenue = $query->where('status', 'livré')->sum('prix_commande');

        // Marge bénéfice = Total des marges des commandes livrées uniquement
        $totalProfit = $query->where('status', 'livré')->sum('marge_benefice');

        // Statistiques des vendeurs
        $totalSellers = User::where('role', 'seller')->count();

        // Commandes récentes pour le graphique
        $recentOrders = $query->latest()->take(10)->get();

        // Données pour le graphique (7 derniers jours)
        $chartData = $this->getChartData($period);

        // Vérification automatique des stocks faibles
        $stockAlerts = $this->getStockAlerts();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalSellers',
            'totalRevenue',
            'totalProfit',
            'recentOrders',
            'chartData',
            'period',
            'startDate',
            'endDate',
            'stockAlerts'
        ));
    }

    public function products()
    {
        $products = \App\Models\Product::with('category')->get();
        return view('admin.products', compact('products'));
    }

    public function sellers()
    {
        // Rediriger vers la gestion des vendeurs via UserController
        return redirect()->route('admin.users.index');
    }

    public function statistics()
    {
        // Logic for generating statistics can be added here
        return view('admin.statistics');
    }

    public function stock()
    {
        $products = \App\Models\Product::all();
        return view('admin.stock', compact('products'));
    }

    /**
     * Vérifier les stocks faibles et créer des alertes
     */
    private function getStockAlerts()
    {
        $alerts = [];

        // Vérifier les produits avec stock faible (≤5)
        $lowStockProducts = Product::where('quantite_stock', '<=', 5)
            ->where('quantite_stock', '>', 0)
            ->with('category')
            ->get();

        // Vérifier les produits en rupture (stock = 0)
        $outOfStockProducts = Product::where('quantite_stock', '<=', 0)
            ->with('category')
            ->get();

        // Vérifier les stocks par couleur si disponible
        $colorStockAlerts = [];
        foreach (Product::whereNotNull('stock_couleurs')->get() as $product) {
            if ($product->stock_couleurs) {
                $stockColors = is_array($product->stock_couleurs) ? $product->stock_couleurs : json_decode($product->stock_couleurs, true);

                if (is_array($stockColors)) {
                    foreach ($stockColors as $color => $quantity) {
                        if ($quantity <= 5 && $quantity > 0) {
                            $colorStockAlerts[] = [
                                'product' => $product,
                                'color' => $color,
                                'quantity' => $quantity,
                                'type' => 'low_stock'
                            ];
                        } elseif ($quantity <= 0) {
                            $colorStockAlerts[] = [
                                'product' => $product,
                                'color' => $color,
                                'quantity' => $quantity,
                                'type' => 'out_of_stock'
                            ];
                        }
                    }
                }
            }
        }

        if (count($lowStockProducts) > 0 || count($outOfStockProducts) > 0 || count($colorStockAlerts) > 0) {
            $alerts = [
                'low_stock' => $lowStockProducts,
                'out_of_stock' => $outOfStockProducts,
                'color_alerts' => $colorStockAlerts,
                'total_alerts' => count($lowStockProducts) + count($outOfStockProducts) + count($colorStockAlerts)
            ];
        }

        return $alerts;
    }

    /**
     * Générer les données pour le graphique des commandes
     */
    private function getChartData($period = 'all')
    {
        $query = Order::query();

        // Appliquer le filtre temporel pour le graphique
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        // Données des 7 derniers jours
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = (clone $query)->whereDate('created_at', $date)->count();
            $revenue = (clone $query)->whereDate('created_at', $date)->sum('prix_commande');

            $chartData[] = [
                'date' => $date->format('d/m'),
                'orders' => $count,
                'revenue' => $revenue
            ];
        }

        return $chartData;
    }
}
