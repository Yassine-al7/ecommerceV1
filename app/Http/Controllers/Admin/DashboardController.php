<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

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
        $totalRevenue = $query->sum('prix_commande');
        $totalProfit = $query->sum('marge_benefice');

        // Statistiques des vendeurs
        $totalSellers = User::where('role', 'seller')->count();

        // Commandes récentes pour le graphique
        $recentOrders = $query->latest()->take(10)->get();

        // Données pour le graphique (7 derniers jours)
        $chartData = $this->getChartData($period);

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalSellers',
            'totalRevenue',
            'totalProfit',
            'recentOrders',
            'chartData',
            'period',
            'startDate',
            'endDate'
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
