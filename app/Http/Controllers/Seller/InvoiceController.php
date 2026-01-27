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

        // Calculer les statistiques selon la nouvelle logique
        $totalRevenue = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->sum('prix_commande');

        // Carte "Payé" = Total des montants de bénéfice payés
        $totalBeneficesPayes = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->where('facturation_status', 'payé')
            ->sum('marge_benefice');

        // Carte "Total Bénéfices" = Montant total de bénéfice non payé
        $totalBeneficesNonPayes = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->where(function($query) {
                $query->where('facturation_status', 'non payé')
                      ->orWhereNull('facturation_status');
            })
            ->sum('marge_benefice');

        // Garder totalPending pour compatibilité (montant des commandes non payées)
        $totalPending = Order::where('seller_id', $userId)
            ->where('status', 'livré')
            ->where(function($query) {
                $query->where('facturation_status', 'non payé')
                      ->orWhereNull('facturation_status');
            })
            ->sum('prix_commande');

        return view('seller.invoices.index', compact(
            'orders',
            'totalRevenue',
            'totalBeneficesPayes',
            'totalPending',
            'totalBeneficesNonPayes'
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
