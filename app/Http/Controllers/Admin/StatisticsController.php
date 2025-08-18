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
        // Statistiques des commandes par statut
        $orderStats = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Produits
        $totalProducts = Product::count();
        $productsByCategory = Product::with('category')
            ->get()
            ->groupBy('categorie_id')
            ->map(function($products) {
                return $products->count();
            });

        // Chiffre d'affaires total
        $totalRevenue = Order::where('status', 'livré')->sum('prix_commande');

        // Top produits vendus - utiliser la table commandes directement
        $topProducts = Product::select('produits.*')
            ->selectRaw('(SELECT COUNT(*) FROM commandes WHERE JSON_EXTRACT(produits, "$[0].product_id") = produits.id) as total_sales')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get();

        // Activités récentes (dernières commandes)
        $recentOrders = Order::with('seller')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.statistics', compact(
            'orderStats',
            'totalProducts',
            'productsByCategory',
            'totalRevenue',
            'topProducts',
            'recentOrders'
        ));
    }

    public function stock()
    {
        $products = Product::with('category')->get();
        return view('admin.stock', compact('products'));
    }
}
