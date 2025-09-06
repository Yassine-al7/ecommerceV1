<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {

        // Top produits vendus - pour graphique (approche simplifiée)
        $topProducts = Product::select('produits.*')
            ->selectRaw('(SELECT COUNT(*) FROM commandes WHERE JSON_CONTAINS(produits, JSON_OBJECT("product_id", produits.id))) as total_sales')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get();

        // Si aucun produit n'a de ventes, garder les vrais produits avec 0 ventes
        if ($topProducts->where('total_sales', '>', 0)->count() == 0) {
            $topProducts = Product::take(5)->get()->map(function($product) {
                $product->total_sales = 0; // Vraies données avec 0 ventes
                return $product;
            });
        }

        // Top vendeurs par ventes - pour graphique
        $topSellers = User::where('role', 'seller')
            ->select('users.*')
            ->selectRaw('(SELECT COUNT(*) FROM commandes WHERE seller_id = users.id) as total_orders')
            ->selectRaw('(SELECT SUM(prix_commande) FROM commandes WHERE seller_id = users.id AND status = "livré") as total_revenue')
            ->orderBy('total_revenue', 'desc')
            ->take(6)
            ->get();

        // Si aucun vendeur n'a de ventes, garder les vrais vendeurs avec 0 ventes
        if ($topSellers->where('total_revenue', '>', 0)->count() == 0) {
            $topSellers = User::where('role', 'seller')->take(6)->get()->map(function($seller) {
                $seller->total_orders = 0;
                $seller->total_revenue = 0; // Vraies données avec 0 ventes
                return $seller;
            });
        }

        // Données réelles des ventes par mois (6 derniers mois) - FORCER TOUS LES MOIS
        $monthlySales = collect();

        // Créer les 6 derniers mois même s'ils n'ont pas de données
        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = now()->subMonths($i)->startOfMonth();
            $endOfMonth = now()->subMonths($i)->endOfMonth();

            // Récupérer les données réelles pour ce mois
            $monthData = Order::where('status', 'livré')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->selectRaw('COUNT(*) as total_orders, SUM(prix_commande) as total_revenue')
                ->first();

            $monthlySales->push([
                'month_name' => $startOfMonth->format('M Y'),
                'total_orders' => (int) ($monthData->total_orders ?? 0),
                'total_revenue' => (float) ($monthData->total_revenue ?? 0)
            ]);
        }

                // Statistiques des nouveaux vendeurs par mois (6 derniers mois)
        $newSellersByMonth = collect();

        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = now()->subMonths($i)->startOfMonth();
            $endOfMonth = now()->subMonths($i)->endOfMonth();

            $newSellersCount = User::where('role', 'seller')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $newSellersByMonth->push([
                'month_name' => $startOfMonth->format('M Y'),
                'new_sellers' => (int) $newSellersCount
            ]);
        }

        // Statistiques globales des vendeurs
        $totalSellers = User::where('role', 'seller')->count();
        $activeSellers = User::where('role', 'seller')
            ->whereHas('orders', function($query) {
                $query->where('created_at', '>=', now()->subMonths(3));
            })
            ->count();
        $newSellersThisMonth = User::where('role', 'seller')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Liste des vendeurs avec leurs statistiques
        $allSellers = User::where('role', 'seller')
            ->withCount(['orders as total_orders'])
            ->withCount(['orders as delivered_orders' => function($query) {
                $query->where('status', 'livré');
            }])
            ->selectRaw('users.*,
                (SELECT SUM(prix_commande) FROM commandes WHERE seller_id = users.id AND status = "livré") as total_revenue,
                (SELECT COUNT(*) FROM commandes WHERE seller_id = users.id AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as orders_last_30_days')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Calculer le vrai chiffre d'affaires total
        $totalRevenue = Order::where('status', 'livré')->sum('prix_commande');
        $totalOrders = Order::where('status', 'livré')->count();

        return view('admin.statistics', compact(
            'topProducts',
            'topSellers',
            'monthlySales',
            'newSellersByMonth',
            'totalSellers',
            'activeSellers',
            'newSellersThisMonth',
            'allSellers',
            'totalRevenue',
            'totalOrders'
        ));
    }

    public function stock()
    {
        // Forcer le rechargement des données depuis la base
        $products = Product::with('category')->get()->fresh();
        return view('admin.stock', compact('products'));
    }
}
