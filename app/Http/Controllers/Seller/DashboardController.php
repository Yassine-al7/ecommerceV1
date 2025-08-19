<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user();

        // KPIs
        $totalAssignedProducts = DB::table('product_user as pu')
            ->join('produits as p', 'pu.product_id', '=', 'p.id')
            ->where('pu.user_id', $seller->id)
            ->count('pu.product_id');

        $totalAdminProducts = DB::table('produits')->count();

        $totalSellerOrders = Order::where('seller_id', $seller->id)->count();

        // Profit vendeur basé sur les commandes livrées
        $deliveredOrders = Order::where('seller_id', $seller->id)
            ->where('status', 'livré')
            ->get();

        $sellerProfit = 0;
        foreach ($deliveredOrders as $order) {
            $decoded = json_decode($order->produits, true);
            $orderCost = 0;
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    if (!isset($item['product_id']) || !isset($item['qty'])) continue;
                    $pivot = DB::table('product_user')
                        ->where('user_id', $seller->id)
                        ->where('product_id', (int)$item['product_id'])
                        ->first();
                    $prixAdmin = $pivot ? (float)$pivot->prix_admin : 0;
                    $orderCost += $prixAdmin * (int)$item['qty'];
                }
            }
            $sellerProfit += max(0, (float)$order->prix_commande - $orderCost);
        }
        // Dernières commandes du vendeur
        $recentOrders = Order::where('seller_id', $seller->id)
            ->latest()
            ->take(5)
            ->get();

        // Dernières assignations de produits au vendeur
        $recentAssignments = DB::table('product_user as pu')
            ->join('produits as p', 'pu.product_id', '=', 'p.id')
            ->where('pu.user_id', $seller->id)
            ->orderBy('pu.created_at', 'desc')
            ->take(5)
            ->get([
                'p.name as product_name',
                'p.tailles as product_sizes',
                'pu.created_at as assigned_at'
            ]);

        return view('seller.dashboard', compact(
            'seller',
            'recentOrders',
            'recentAssignments',
            'totalAssignedProducts',
            'totalAdminProducts',
            'totalSellerOrders',
            'sellerProfit'
        ));
    }
}
