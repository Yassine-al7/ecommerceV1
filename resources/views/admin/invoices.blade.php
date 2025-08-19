<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturation</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

@php
use Illuminate\Support\Facades\DB;
@endphp

    @section('title', 'Gestion des Facturations')

    @section('content')
    <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestion des Facturations</h1>
                <p class="text-gray-600">Gérez les paiements des vendeurs et le suivi des factures</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Statistiques globales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-600">Vendeurs Actifs</p>
                            <p class="text-2xl font-bold text-blue-900" id="totalVendeurs">{{ $orders->pluck('seller_id')->unique()->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-600">Commandes Livrées</p>
                            <p class="text-2xl font-bold text-green-900" id="totalCommandes">{{ $orders->total() }}</p>
                        </div>
                    </div>
                </div>

                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-money-bill-wave text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-600">Total Commandes</p>
                        <p class="text-2xl font-bold text-yellow-900" id="totalVentes">{{ number_format($orders->sum('prix_commande'), 0) }} MAD</p>
                        <p class="text-xs text-yellow-600">Chiffre d'affaires global</p>
                    </div>
                </div>
            </div>



            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600">Bénéfice Total Vendeurs</p>
                        @php
                            $totalCoutVendeurs = 0;
                            foreach ($orders as $order) {
                                if (is_array($order->produits)) {
                                    foreach ($order->produits as $produit) {
                                        if (is_array($produit) && isset($produit['product_id']) && isset($produit['qty'])) {
                                            $productUser = DB::table('product_user')
                                                ->where('product_id', $produit['product_id'])
                                                ->where('user_id', $order->seller_id)
                                                ->first();
                                            if ($productUser) {
                                                $totalCoutVendeurs += ($productUser->prix_admin ?? 0) * $produit['qty'];
                                            }
                                        }
                                    }
                                }
                            }
                            $totalBeneficeVendeurs = $orders->sum('prix_commande') - $totalCoutVendeurs;
                            $pourcentageBenefice = $orders->sum('prix_commande') > 0 ? round(($totalBeneficeVendeurs / $orders->sum('prix_commande')) * 100, 1) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-green-900" id="totalBenefices">{{ number_format($totalBeneficeVendeurs, 0) }} MAD</p>
                        <p class="text-xs text-green-600">Marge: {{ $pourcentageBenefice }}%</p>
                    </div>
                </div>
            </div>
            </div>

            <!-- Section des actions et filtres -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <!-- Boutons d'action -->
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.invoices.export') }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Exporter CSV
                        </a>
                        <button onclick="resetFilters()"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-refresh mr-2"></i>Réinitialiser
                        </button>
                    </div>

                    <!-- Résumé des filtres actifs -->
                    <div class="text-sm text-gray-600">
                        <span id="filterSummary">Tous les vendeurs affichés</span>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vendeur *</label>
                        <select id="sellerFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les vendeurs</option>
                            @foreach($orders->pluck('seller_id')->unique() as $sellerId)
                                @php $seller = App\Models\User::find($sellerId); @endphp
                                @if($seller)
                                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Sélectionnez un vendeur pour voir ses ventes spécifiques</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut de Paiement</label>
                        <select id="paymentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les statuts</option>
                            <option value="payé">Payé</option>
                            <option value="non payé">Non payé</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                        <input type="text" id="searchFilter" placeholder="Nom du client..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Tableau des factures -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Factures des Vendeurs</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <span id="tableSummary">Total: {{ $orders->total() }} commandes livrées</span>
                    </p>
        </div>

                @if($orders->count() > 0)
            <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">
                                    Vendeur
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">
                                    Client
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                    Produits
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                    Prix Commande
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                    Coût Vendeur
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                    Bénéfice Vendeur
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                    Statut
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                    Actions
                                </th>
                        </tr>
                    </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="invoicesTableBody">
                        @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50 invoice-row"
                                        data-seller-id="{{ $order->seller_id }}"
                                        data-payment-status="{{ $order->facturation_status ?? 'non payé' }}"
                                        data-client-name="{{ strtolower($order->nom_client) }}">
                                                                            <!-- Vendeur -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <i class="fas fa-user text-blue-600"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $order->seller->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        ID: {{ $order->seller_id ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                                                            <!-- Client -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $order->nom_client }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->ville }}</div>
                                        </td>

                                                                                                                <!-- Produits -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if(is_array($order->produits))
                                                @foreach($order->produits as $produit)
                                                    @php
                                                        $productName = '';
                                                        $quantity = '';

                                                        if (is_array($produit)) {
                                                            if (isset($produit['product_id'])) {
                                                                $product = App\Models\Product::find($produit['product_id']);
                                                                $productName = $product ? $product->name : 'Produit ID: ' . $produit['product_id'];
                                                            }
                                                            if (isset($produit['qty'])) {
                                                                $quantity = ' (x' . $produit['qty'] . ')';
                                                            }
                                                        } else {
                                                            $productName = $produit;
                                                        }
                                                    @endphp
                                                    <div class="mb-1">
                                                        <span class="font-medium">{{ $productName }}</span>
                                                        @if($quantity)
                                                            <span class="text-blue-600">{{ $quantity }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @elseif(is_string($order->produits) && json_decode($order->produits))
                                                @php
                                                    $decodedProducts = json_decode($order->produits, true);
                                                @endphp
                                                @if(is_array($decodedProducts))
                                                    @foreach($decodedProducts as $produit)
                                                        @php
                                                            $productName = '';
                                                            $quantity = '';

                                                            if (is_array($produit)) {
                                                                if (isset($produit['product_id'])) {
                                                                    $product = App\Models\Product::find($produit['product_id']);
                                                                    $productName = $product ? $product->name : 'Produit ID: ' . $produit['product_id'];
                                                                }
                                                                if (isset($produit['qty'])) {
                                                                    $quantity = ' (x' . $produit['qty'] . ')';
                                                                }
                                                            } else {
                                                                $productName = $produit;
                                                            }
                                                        @endphp
                                                        <div class="mb-1">
                                                            <span class="font-medium">{{ $productName }}</span>
                                                            @if($quantity)
                                                                <span class="text-blue-600">{{ $quantity }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    {{ $order->produits }}
                                                @endif
                                            @else
                                                {{ $order->produits }}
                                            @endif
                                        </div>
                                    </td>

                                                                                                                <!-- Prix Commande (ce que le client paie) -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-blue-600">
                                            {{ number_format($order->prix_commande, 0) }} MAD
                                        </div>
                                    </td>

                                                                                                                                                <!-- Coût Vendeur (ce que le vendeur paie pour acheter) -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                                                                                                                                                @php
                                            $coutVendeur = 0;
                                            $quantiteTotale = 0;

                                            // DÉCODER LE JSON EN TABLEAU
                                            $produits = is_string($order->produits) ? json_decode($order->produits, true) : $order->produits;

                                            if (is_array($produits)) {
                                                foreach ($produits as $produit) {
                                                    if (is_array($produit) && isset($produit['product_id']) && isset($produit['qty'])) {
                                                        // Debug: afficher les valeurs exactes
                                                        $productId = $produit['product_id'];
                                                        $sellerId = $order->seller_id;
                                                        $qty = $produit['qty'];

                                                        // Récupérer le prix admin (ce que le vendeur paie pour acheter) depuis la table pivot
                                                        $productUser = DB::table('product_user')
                                                            ->where('product_id', $productId)
                                                            ->where('user_id', $sellerId)
                                                            ->first();

                                                        // Debug: afficher le résultat de la requête
                                                        if ($productUser) {
                                                            $coutVendeur += ($productUser->prix_admin ?? 0) * $qty;
                                                            $quantiteTotale += $qty;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        <div class="text-sm">
                                            <div class="font-semibold text-orange-600">
                                                {{ number_format($coutVendeur, 0) }} MAD
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Qty: {{ $quantiteTotale }}
                                            </div>

                                        </div>
                                    </td>

                                    <!-- Bénéfice Vendeur (prix client - coût vendeur) -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @php
                                            $beneficeVendeur = $order->prix_commande - $coutVendeur; // Bénéfice du vendeur
                                            $pourcentageBenefice = $order->prix_commande > 0 ? round(($beneficeVendeur / $order->prix_commande) * 100, 1) : 0;
                                        @endphp
                                        <div class="text-sm">
                                            <div class="font-semibold text-green-600">
                                                {{ number_format($beneficeVendeur, 0) }} MAD
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Marge: {{ $pourcentageBenefice }}%
                                            </div>
                                        </div>
                                    </td>

                                                                            <!-- Statut Paiement -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $order->facturation_status == 'payé' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($order->facturation_status ?? 'non payé') }}
                                            </span>
                                        </td>

                                                                            <!-- Actions -->
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="togglePaymentStatus({{ $order->id }})"
                                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>

                                            <!-- Formulaire de modification du statut (caché par défaut) -->
                                            <form id="paymentForm{{ $order->id }}" method="POST"
                                                  action="{{ route('admin.invoices.update-status', $order->id) }}"
                                                  class="hidden mt-2">
                                    @csrf
                                    @method('PATCH')
                                                <select name="facturation_status" onchange="this.form.submit()"
                                                        class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 w-full">
                                                    <option value="non payé" @selected($order->facturation_status == 'non payé')>Non payé</option>
                                                    <option value="payé" @selected($order->facturation_status == 'payé')>Payé</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
                    </div>
                @else
                    <!-- État vide -->
                    <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-file-invoice text-6xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-800 mb-2">Aucune facture trouvée</h3>
                        <p class="text-gray-600">Il n'y a pas encore de commandes livrées à facturer.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    // Variables globales pour les données
    let allOrders = @json($orders->items());
    let currentFilteredOrders = @json($orders->items());

    // Filtrage des factures
    document.addEventListener('DOMContentLoaded', function() {
        const sellerFilter = document.getElementById('sellerFilter');
        const paymentFilter = document.getElementById('paymentFilter');
        const searchFilter = document.getElementById('searchFilter');

        // Écouteurs d'événements
        sellerFilter.addEventListener('change', filterInvoices);
        paymentFilter.addEventListener('change', filterInvoices);
        searchFilter.addEventListener('input', filterInvoices);

        // Filtrage initial
        filterInvoices();
    });

    function filterInvoices() {
        const selectedSeller = document.getElementById('sellerFilter').value;
        const selectedPayment = document.getElementById('paymentFilter').value;
        const searchTerm = document.getElementById('searchFilter').value.toLowerCase();

        const tableBody = document.getElementById('invoicesTableBody');
        const rows = tableBody.querySelectorAll('.invoice-row');

        let visibleCount = 0;
        let totalRevenue = 0;
        let totalCost = 0;
        let totalProfit = 0;

        rows.forEach(row => {
            const sellerId = row.dataset.sellerId;
            const paymentStatus = row.dataset.paymentStatus;
            const clientName = row.dataset.clientName;

            // Vérifier les filtres
            const sellerMatch = !selectedSeller || sellerId === selectedSeller;
            const paymentMatch = !selectedPayment || paymentStatus === selectedPayment || selectedPayment === 'Tous les statuts';
            const searchMatch = !searchTerm || clientName.includes(searchTerm);

            if (sellerMatch && paymentMatch && searchMatch) {
                row.style.display = 'table-row';
                visibleCount++;

                // Calculer les totaux pour les lignes visibles
                const prixCommande = parseFloat(row.querySelector('td:nth-child(4) .text-blue-600').textContent.replace(' MAD', '').replace(',', ''));
                const coutVendeur = parseFloat(row.querySelector('td:nth-child(5) .text-orange-600').textContent.replace(' MAD', '').replace(',', ''));
                const beneficeVendeur = parseFloat(row.querySelector('td:nth-child(6) .text-green-600').textContent.replace(' MAD', '').replace(',', ''));

                totalRevenue += prixCommande; // Total des commandes
                totalCost += coutVendeur; // Coût total des vendeurs
                totalProfit += beneficeVendeur; // Bénéfice total des vendeurs
            } else {
                row.style.display = 'none';
            }
        });

        // Mettre à jour les statistiques
        updateStatistics(visibleCount, totalRevenue, totalCost, totalProfit);

        // Mettre à jour le résumé
        updateFilterSummary(selectedSeller, selectedPayment, searchTerm, visibleCount);
    }

            function updateStatistics(visibleCount, totalRevenue, totalCost, totalProfit) {
        // Mettre à jour le nombre de commandes
        document.getElementById('totalCommandes').textContent = visibleCount;

        // Mettre à jour le total des commandes (chiffre d'affaires)
        document.getElementById('totalVentes').textContent = numberFormat(totalRevenue) + ' MAD';

        // Mettre à jour le coût total des vendeurs
        document.getElementById('totalCoutVendeurs').textContent = numberFormat(totalCost) + ' MAD';

        // Mettre à jour le bénéfice admin
        document.getElementById('totalBenefices').textContent = numberFormat(totalProfit) + ' MAD';

        // Calculer et mettre à jour le pourcentage de marge admin
        const pourcentageMarge = totalRevenue > 0 ? Math.round((totalProfit / totalRevenue) * 100 * 10) / 10 : 0;
        const beneficesElement = document.getElementById('totalBenefices');
        const parentDiv = beneficesElement.parentElement;

        // Mettre à jour l'élément de pourcentage de marge
        let margeElement = parentDiv.querySelector('.text-xs');
        if (margeElement) {
            margeElement.textContent = `Marge: ${pourcentageMarge}%`;
        }

        // Mettre à jour le résumé du tableau
        document.getElementById('tableSummary').textContent = `Total: ${visibleCount} commandes livrées`;
    }

    function updateFilterSummary(selectedSeller, selectedPayment, searchTerm, visibleCount) {
        let summary = '';

        if (selectedSeller) {
            const sellerName = document.getElementById('sellerFilter').options[document.getElementById('sellerFilter').selectedIndex].text;
            summary = `Vendeur: ${sellerName}`;
        } else {
            summary = 'Tous les vendeurs';
        }

        if (selectedPayment) {
            summary += ` | Statut: ${selectedPayment}`;
        }

        if (searchTerm) {
            summary += ` | Recherche: "${searchTerm}"`;
        }

        summary += ` | ${visibleCount} résultat(s)`;

        document.getElementById('filterSummary').textContent = summary;
    }

    function resetFilters() {
        document.getElementById('sellerFilter').value = '';
        document.getElementById('paymentFilter').value = '';
        document.getElementById('searchFilter').value = '';
        filterInvoices();
    }

    function numberFormat(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // Fonction pour afficher/masquer le formulaire de modification du statut
    function togglePaymentStatus(orderId) {
        const form = document.getElementById(`paymentForm${orderId}`);
        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
        } else {
            form.classList.add('hidden');
        }
    }
    </script>
    @endsection
</body>
</html>


