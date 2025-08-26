<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الفوترة</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <style>
        /* Optimisations pour le tableau */
        .table-container {
            max-height: 70vh;
            overflow-y: auto;
            overflow-x: auto;
        }

        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* En-tête fixe du tableau */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #f9fafb;
        }

        /* Optimisation des cellules */
        .table-cell {
            padding: 0.75rem 0.75rem;
            vertical-align: top;
        }

        /* Responsive pour petits écrans */
        @media (max-width: 768px) {
            .table-container {
                max-height: 60vh;
            }

            .table-cell {
                padding: 0.5rem 0.5rem;
            }
        }

        /* Animation de chargement */
        .loading-opacity {
            transition: opacity 0.3s ease-in-out;
        }

        .loading-opacity.opacity-75 {
            opacity: 0.75;
        }
    </style>
</head>
<body>
    @extends('layouts.app')

@php
use Illuminate\Support\Facades\DB;
@endphp

    @section('title', 'إدارة الفوترة')

    @section('content')
    <div class="min-h-screen bg-gray-50 py-4 md:py-8">
    <div class="container-responsive">
            <!-- En-tête -->
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 text-center md:text-left">إدارة الفوترة</h1>
                <p class="text-gray-600 text-center md:text-left">إدارة مدفوعات البائعين وتتبع الفواتير</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success mb-6">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Statistiques globales -->
            <div class="card-grid mb-6 md:mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="flex items-center">
                        <div class="p-2 md:p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-blue-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="ml-3 md:ml-4">
                            <p class="text-sm font-medium text-blue-600">البائعون النشطون</p>
                            <p class="text-xl md:text-2xl font-bold text-blue-900" id="totalVendeurs">{{ $orders->pluck('seller_id')->unique()->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="flex items-center">
                        <div class="p-2 md:p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-shopping-cart text-green-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="ml-3 md:ml-4">
                            <p class="text-sm font-medium text-green-600">الطلبات المسلمة</p>
                            <p class="text-xl md:text-2xl font-bold text-green-900" id="totalCommandes">{{ $orders->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="flex items-center">
                        <div class="p-2 md:p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-money-bill-wave text-yellow-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="ml-3 md:ml-4">
                            <p class="text-sm font-medium text-yellow-600">إجمالي الطلبات</p>
                            <p class="text-xl md:text-2xl font-bold text-yellow-900" id="totalVentes">{{ number_format($orders->sum('prix_commande'), 0) }} MAD</p>
                            <p class="text-xs text-yellow-600">إجمالي رقم المعاملات</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="flex items-center">
                        <div class="p-2 md:p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-chart-line text-green-600 text-lg md:text-xl"></i>
                        </div>
                        <div class="ml-3 md:ml-4">
                            <p class="text-sm font-medium text-green-600" id="margeBeneficeTitle">إجمالي هامش الربح</p>
                            <p class="text-xl md:text-2xl font-bold text-green-900" id="totalBenefices">{{ number_format($orders->sum('marge_benefice'), 0) }} MAD</p>
                            <p class="text-xs text-green-600" id="pourcentageMarge">
                                @php
                                    $totalRevenue = $orders->sum('prix_commande');
                                    $totalMargeBenefice = $orders->sum('marge_benefice');
                                    $pourcentageMarge = $totalRevenue > 0 ? round(($totalMargeBenefice / $totalRevenue) * 100, 1) : 0;
                                @endphp
                                الهامش الكلي: {{ $pourcentageMarge }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section des actions et filtres -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6">
                <!-- Boutons d'action -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                    <div class="actions-buttons">
                        <a href="{{ route('admin.invoices.export') }}"
                           class="btn bg-green-600 hover:bg-green-700 text-white">
                            <i class="fas fa-download mr-2"></i>تصدير CSV
                        </a>
                        <button onclick="resetFilters()"
                                class="btn bg-gray-600 hover:bg-gray-700 text-white">
                            <i class="fas fa-refresh mr-2"></i>إعادة التعيين
                        </button>
                    </div>

                    <!-- Résumé des filtres actifs -->
                    <div class="text-sm text-gray-600 text-center md:text-left">
                        <span id="filterSummary">عرض جميع البائعين</span>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label class="form-label">البائع *</label>
                        <select id="sellerFilter" class="form-input">
                            <option value="">كل البائعين</option>
                            @foreach($orders->pluck('seller_id')->unique() as $sellerId)
                                @php $seller = App\Models\User::find($sellerId); @endphp
                                @if($seller)
                                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">اختر بائعًا لعرض مبيعاته</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">حالة الدفع</label>
                        <select id="paymentFilter" class="form-input">
                            <option value="">كل الحالات</option>
                            <option value="payé">مدفوع</option>
                            <option value="non payé">غير مدفوع</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">بحث</label>
                        <input type="text" id="searchFilter" placeholder="اسم العميل..."
                               class="form-input">
                    </div>
                </div>
            </div>

            <!-- Tableau des factures -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-200">
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 text-center md:text-left">فواتير البائعين</h2>
                    <p class="text-sm text-gray-600 mt-1 text-center md:text-left">
                        <span id="tableSummary">المجموع: {{ $orders->total() }} طلبات مسلمة</span>
                    </p>
                    <!-- Note explicative sur les calculs -->
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                            <div class="text-xs text-blue-700">
                                <p class="font-medium mb-1">الفرق بين هامش الربح والربح الفعلي للبائع:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>هامش الربح:</strong> سعر بيع العميل - سعر شراء البائع (بدون التوصيل)</li>
                                    <li><strong>الربح الفعلي للبائع:</strong> سعر بيع العميل - سعر شراء البائع - سعر التوصيل</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="w-full divide-y divide-gray-200 min-w-full">
                        <thead class="sticky-header">
                            <tr>
                                <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    البائع
                                </th>
                                <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32 hidden-mobile">
                                    RIB
                                </th>
                                <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    العميل
                                </th>
                                <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48 hidden-mobile">
                                    المنتجات
                                </th>
                                <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                    سعر الطلب
                                </th>
                                <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                    هامش الربح
                                </th>
                                <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    الحالة
                                </th>
                                <th class="table-cell text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    إجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="invoicesTableBody">
                        @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50 invoice-row"
                                        data-seller-id="{{ $order->seller_id }}"
                                        data-payment-status="{{ $order->facturation_status ?? 'non payé' }}"
                                        data-client-name="{{ strtolower($order->nom_client) }}"
                                        data-prix-commande="{{ $order->prix_commande }}"
                                        data-marge-benefice="{{ $order->marge_benefice }}">
                                            <!-- Vendeur -->
                                    <td class="table-cell whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $order->seller->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 hidden-mobile">
                                                        ID: {{ $order->seller_id ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                            <!-- RIB -->
                                    <td class="table-cell whitespace-nowrap hidden-mobile">
                                        <div class="text-sm text-gray-900">
                                            @if($order->seller && $order->seller->rib)
                                                <span class="font-mono bg-gray-100 px-2 py-1 rounded text-xs">
                                                    {{ $order->seller->rib }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">غير محدد</span>
                                            @endif
                                        </div>
                                    </td>

                                            <!-- Client -->
                                    <td class="table-cell whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $order->nom_client }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->ville }}</div>
                                        </td>

                                            <!-- Produits -->
                                    <td class="table-cell hidden-mobile">
                                        <div class="text-sm text-gray-900 max-w-xs">
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

                                            <!-- Prix Commande -->
                                    <td class="table-cell whitespace-nowrap">
                                        <div class="text-sm font-semibold text-blue-600">
                                            {{ number_format($order->prix_commande, 0) }} MAD
                                        </div>
                                    </td>

                                            <!-- Marge Bénéfice -->
                                    <td class="table-cell whitespace-nowrap">
                                        <div class="text-sm">
                                            <div class="font-semibold text-emerald-600">
                                                {{ number_format($order->marge_benefice, 0) }} DH
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                هامش الربح
                                            </div>
                                        </div>
                                    </td>

                                            <!-- Statut Paiement -->
                                    <td class="table-cell whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $order->facturation_status == 'payé' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($order->facturation_status ?? 'non payé') }}
                                            </span>
                                        </td>

                                            <!-- Actions -->
                                    <td class="table-cell whitespace-nowrap text-sm font-medium">
                                            <button onclick="togglePaymentStatus({{ $order->id }})"
                                                    class="btn btn-sm text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100">
                                                <i class="fas fa-edit"></i> <span class="hidden sm:inline">تعديل</span>
                                            </button>

                                            <form id="paymentForm{{ $order->id }}" method="POST"
                                                  action="{{ route('admin.invoices.update-status', $order->id) }}"
                                                  class="hidden mt-2">
                                    @csrf
                                    @method('PATCH')
                                                <select name="facturation_status" onchange="this.form.submit()"
                                                        class="form-input text-sm">
                                                    <option value="non payé" @selected($order->facturation_status == 'non payé')>غير مدفوع</option>
                                                    <option value="payé" @selected($order->facturation_status == 'payé')>مدفوع</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

                    <!-- Pagination -->
                    <div class="p-4 md:p-6 border-t border-gray-200">
                        <div class="pagination">
                            {{ $orders->links() }}
                        </div>
                    </div>
                @else
                    <!-- État vide -->
                    <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 text-center">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-file-invoice text-4xl md:text-6xl"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-medium text-gray-800 mb-2">لا توجد فواتير</h3>
                        <p class="text-gray-600">لا توجد طلبات مسلمة للفوترة حتى الآن.</p>
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
        searchFilter.addEventListener('input', debounce(filterInvoices, 300));

        // Filtrage initial
        filterInvoices();
    });

    // Fonction de debounce pour la recherche
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function filterInvoices() {
        const selectedSeller = document.getElementById('sellerFilter').value;
        const selectedPayment = document.getElementById('paymentFilter').value;
        const searchTerm = document.getElementById('searchFilter').value.toLowerCase();

        // Debug: afficher les valeurs des filtres
        console.log('Filtres appliqués:', {
            seller: selectedSeller,
            payment: selectedPayment,
            search: searchTerm
        });

        // Mettre à jour les statistiques via AJAX
        updateStatisticsViaAjax(selectedSeller, selectedPayment, searchTerm);

        // Filtrage local pour l'affichage du tableau
        filterTableLocally(selectedSeller, selectedPayment, searchTerm);
    }

    function updateStatisticsViaAjax(sellerId, paymentStatus, searchTerm) {
        // Afficher un indicateur de chargement
        showLoadingIndicator();

        // Préparer les paramètres de la requête
        const params = new URLSearchParams();
        if (sellerId) params.append('seller_id', sellerId);
        if (paymentStatus) params.append('payment_status', paymentStatus);
        if (searchTerm) params.append('search', searchTerm);

        // Appeler la route AJAX
        fetch(`{{ route('admin.invoices.filtered-data') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les statistiques avec les données du serveur
            updateStatistics(data.totals.count, data.totals.revenue, data.totals.marge_benefice);

            // Mettre à jour le résumé
            updateFilterSummary(sellerId, paymentStatus, searchTerm, data.totals.count);

            // Cacher l'indicateur de chargement
            hideLoadingIndicator();
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour des statistiques:', error);
            hideLoadingIndicator();

            // Fallback au filtrage local
            filterTableLocally(sellerId, paymentStatus, searchTerm);
        });
    }

    function filterTableLocally(selectedSeller, selectedPayment, searchTerm) {
        const tableBody = document.getElementById('invoicesTableBody');
        const rows = tableBody.querySelectorAll('.invoice-row');

        let visibleCount = 0;
        let totalRevenue = 0;
        let totalMargeBenefice = 0;

        console.log('Début du filtrage local...');

        rows.forEach((row, index) => {
            const sellerId = row.dataset.sellerId;
            const paymentStatus = row.dataset.paymentStatus;
            const clientName = row.dataset.clientName;

            // Vérifier les filtres
            const sellerMatch = !selectedSeller || sellerId === selectedSeller;

            // Correction de la logique de comparaison des statuts de paiement
            let paymentMatch = true;
            if (selectedPayment) {
                if (selectedPayment === 'non payé') {
                    paymentMatch = paymentStatus === 'non payé' || paymentStatus === null || paymentStatus === '';
                } else if (selectedPayment === 'payé') {
                    paymentMatch = paymentStatus === 'payé';
                }
            }

            const searchMatch = !searchTerm || clientName.includes(searchTerm);

            if (sellerMatch && paymentMatch && searchMatch) {
                row.style.display = 'table-row';
                visibleCount++;

                // Utiliser les données stockées dans les attributs data
                const prixCommande = parseFloat(row.dataset.prixCommande);
                const margeBenefice = parseFloat(row.dataset.margeBenefice);

                totalRevenue += prixCommande; // Total des commandes
                totalMargeBenefice += margeBenefice; // Total de la marge bénéfice (toujours)

                console.log(`Ligne ${index + 1} visible:`, {
                    client: clientName,
                    status: paymentStatus,
                    prix: prixCommande,
                    marge: margeBenefice,
                    selectedPayment: selectedPayment
                });
            } else {
                row.style.display = 'none';
                console.log(`Ligne ${index + 1} masquée:`, {
                    client: clientName,
                    status: paymentStatus,
                    sellerMatch,
                    paymentMatch,
                    searchMatch
                });
            }
        });

        console.log('Résultats du filtrage local:', {
            visibleCount,
            totalRevenue,
            totalMargeBenefice,
            selectedPayment: selectedPayment
        });

        // Mettre à jour le résumé du tableau
        document.getElementById('tableSummary').textContent = `المجموع: ${visibleCount} طلبات مسلمة`;

        // Mettre à jour les statistiques avec les données filtrées localement
        updateStatistics(visibleCount, totalRevenue, totalMargeBenefice);
    }

    function updateStatistics(visibleCount, totalRevenue, totalMargeBenefice) {
        // Mettre à jour le nombre de commandes
        document.getElementById('totalCommandes').textContent = visibleCount;

        // Mettre à jour le total des commandes (chiffre d'affaires)
        document.getElementById('totalVentes').textContent = numberFormat(totalRevenue) + ' MAD';

        // Mettre à jour le total de la marge bénéfice
        document.getElementById('totalBenefices').textContent = numberFormat(totalMargeBenefice) + ' MAD';

        // Mettre à jour le titre de la carte - toujours "Total Marge Bénéfice"
        const margeTitle = document.getElementById('margeBeneficeTitle');
        const selectedPayment = document.getElementById('paymentFilter').value;

        if (margeTitle) {
            margeTitle.textContent = 'إجمالي هامش الربح';
        }

        // Calculer et mettre à jour le pourcentage de marge bénéfice
        const pourcentageMarge = totalRevenue > 0 ? Math.round((totalMargeBenefice / totalRevenue) * 100 * 10) / 10 : 0;

        // Mettre à jour l'élément de pourcentage de marge
        const pourcentageElement = document.getElementById('pourcentageMarge');
        if (pourcentageElement) {
            if (selectedPayment === 'non payé') {
                pourcentageElement.textContent = `هامش ربح الطلبات غير المدفوعة: ${pourcentageMarge}%`;
            } else if (selectedPayment === 'payé') {
                pourcentageElement.textContent = `هامش ربح الطلبات المدفوعة: ${pourcentageMarge}%`;
            } else {
                pourcentageElement.textContent = `الهامش الكلي: ${pourcentageMarge}%`;
            }
        }

        // Mettre à jour le résumé du tableau
        document.getElementById('tableSummary').textContent = `المجموع: ${visibleCount} طلبات مسلمة`;
    }

    function updateFilterSummary(selectedSeller, selectedPayment, searchTerm, visibleCount) {
        let summary = '';

        if (selectedSeller) {
            const sellerName = document.getElementById('sellerFilter').options[document.getElementById('sellerFilter').selectedIndex].text;
            summary = `البائع: ${sellerName}`;
        } else {
            summary = 'كل البائعين';
        }

        if (selectedPayment) {
            summary += ` | الحالة: ${selectedPayment}`;
        }

        if (searchTerm) {
            summary += ` | البحث: "${searchTerm}"`;
        }

        summary += ` | ${visibleCount} نتيجة`;

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

    function showLoadingIndicator() {
        // Ajouter une classe de chargement aux cartes des statistiques
        const statCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-sm');
        statCards.forEach(card => {
            card.classList.add('loading-opacity', 'opacity-75');
        });
    }

    function hideLoadingIndicator() {
        // Retirer la classe de chargement des cartes des statistiques
        const statCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-sm');
        statCards.forEach(card => {
            card.classList.remove('loading-opacity', 'opacity-75');
        });
    }
    </script>
    @endsection
</body>
</html>


