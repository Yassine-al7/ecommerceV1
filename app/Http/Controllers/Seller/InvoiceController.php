<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher la facturation du vendeur
     */
    public function index()
    {
        $userId = auth()->id();

        // Récupérer toutes les commandes livrées du vendeur
        $orders = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->with(['seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculer les statistiques
        $totalRevenue = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->sum('prix_commande');

        $totalPaid = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->where('facturation_status', 'payé')
            ->sum('prix_commande');

        $totalPending = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->where('facturation_status', 'non payé')
            ->sum('prix_commande');

        $totalBenefices = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->sum('marge_benefice');

        return view('seller.invoices.index', compact(
            'orders',
            'totalRevenue',
            'totalPaid',
            'totalPending',
            'totalBenefices'
        ));
    }

    /**
     * Afficher une facture spécifique
     */
    public function show($id)
    {
        $userId = auth()->id();

        $order = Order::where('seller_id', $userId)
            ->where('id', $id)
            ->where('status', 'livré')
            ->with(['seller'])
            ->firstOrFail();

        return view('seller.invoices.show', compact('order'));
    }
}
