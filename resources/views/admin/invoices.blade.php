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

    @section('title', 'Gestion des Facturations')

    @section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                            <p class="text-sm font-medium text-yellow-600">Total Ventes</p>
                            <p class="text-2xl font-bold text-yellow-900" id="totalVentes">{{ number_format($orders->sum('prix_commande'), 0) }} MAD</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-600">Bénéfices</p>
                            <p class="text-2xl font-bold text-purple-900" id="totalBenefices">{{ number_format($orders->sum('prix_commande') - $orders->sum('prix_produit'), 0) }} MAD</p>
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
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vendeur
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Client
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produits
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prix Vente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Prix Produit
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bénéfice
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Statut Paiement
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $order->nom_client }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->ville }}</div>
                                        </td>

                                        <!-- Produits -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if(is_array($order->produits))
                                                    @foreach(array_slice($order->produits, 0, 2) as $produit)
                                                        <div class="mb-1">{{ $produit }}</div>
                                                    @endforeach
                                                    @if(count($order->produits) > 2)
                                                        <div class="text-xs text-gray-500">+{{ count($order->produits) - 2 }} autres</div>
                                                    @endif
                                                @else
                                                    {{ $order->produits }}
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Prix Vente -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-green-600">
                                                {{ number_format($order->prix_commande, 0) }} MAD
                                            </div>
                                        </td>

                                        <!-- Prix Produit -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">
                                                {{ number_format($order->prix_produit, 0) }} MAD
                                            </div>
                                        </td>

                                        <!-- Bénéfice -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $benefice = $order->prix_commande - $order->prix_produit;
                                            @endphp
                                            <div class="text-sm font-semibold {{ $benefice >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($benefice, 0) }} MAD
                                            </div>
                                        </td>

                                        <!-- Statut Paiement -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $order->facturation_status == 'payé' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($order->facturation_status ?? 'non payé') }}
                                            </span>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
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
            const paymentMatch = !selectedPayment || paymentStatus.includes(selectedPayment);
            const searchMatch = !searchTerm || clientName.includes(searchTerm);

            if (sellerMatch && paymentMatch && searchMatch) {
                row.style.display = 'table-row';
                visibleCount++;

                // Calculer les totaux pour les lignes visibles
                const prixVente = parseFloat(row.querySelector('td:nth-child(4) .text-green-600').textContent.replace(' MAD', '').replace(',', ''));
                const prixProduit = parseFloat(row.querySelector('td:nth-child(5) .text-gray-600').textContent.replace(' MAD', '').replace(',', ''));

                totalRevenue += prixVente;
                totalCost += prixProduit;
                totalProfit += (prixVente - prixProduit);
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

        // Mettre à jour le total des ventes
        document.getElementById('totalVentes').textContent = numberFormat(totalRevenue) + ' MAD';

        // Mettre à jour les bénéfices
        document.getElementById('totalBenefices').textContent = numberFormat(totalProfit) + ' MAD';

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


