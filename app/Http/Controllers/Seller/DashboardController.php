<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\AdminMessage;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user();
        $sellerId = $seller->id;

        // === STATISTIQUES PRODUITS ===
        $totalAssignedProducts = DB::table('product_user as pu')
            ->join('produits as p', 'pu.product_id', '=', 'p.id')
            ->where('pu.user_id', $sellerId)
            ->count('pu.product_id');

        // === STATISTIQUES COMMANDES ===
        $totalSellerOrders = Order::where('seller_id', $sellerId)->count();
        $ordersEnAttente = Order::where('seller_id', $sellerId)->where('status', 'en attente')->count();
        $ordersLivrees = Order::where('seller_id', $sellerId)->where('status', 'livré')->count();
        $ordersCancelled = Order::where('seller_id', $sellerId)->where('status', 'annulé')->count();

        // === STATISTIQUES FINANCIÈRES ===
        // Chiffre d'affaires (commandes livrées)
        $totalRevenue = Order::where('seller_id', $sellerId)
            ->where('status', 'livré')
            ->sum('prix_commande');



        // Paiements reçus et en attente (basés sur le bénéfice réel du vendeur)
        $totalPaid = Order::where('seller_id', $sellerId)
            ->where('status', 'livré')
            ->where('facturation_status', 'payé')
            ->sum('marge_benefice');

        $totalPending = Order::where('seller_id', $sellerId)
            ->where('status', 'livré')
            ->where(function($q) {
                $q->where('facturation_status', 'non payé')
                  ->orWhereNull('facturation_status');
            })
            ->sum('marge_benefice');

        // === COMMANDES RÉCENTES ===
        $recentOrders = Order::where('seller_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // === COMMANDES PAR STATUT (pour le graphique) ===
        $ordersByStatus = [
            'en_attente' => $ordersEnAttente,
            'livre' => $ordersLivrees,
            'annule' => $ordersCancelled
        ];

        // === MESSAGES ADMIN ===
        $adminMessages = AdminMessage::active()
            ->forRole('seller')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('seller.dashboard', compact(
            'seller',
            'totalAssignedProducts',
            'totalSellerOrders',
            'ordersEnAttente',
            'ordersLivrees',
            'ordersCancelled',
            'totalRevenue',
            'totalPaid',
            'totalPending',
            'recentOrders',
            'ordersByStatus',
            'adminMessages'
        ));
    }
}
