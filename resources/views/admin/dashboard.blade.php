@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="min-h-screen bg-gray-50 py-4 md:py-8">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Tableau de bord Admin</h1>
                <p class="text-gray-600">Vue d'ensemble de l'activité et accès rapide aux sections clés</p>
            </div>

            <!-- Alertes de Stock -->
            @include('components.stock-dashboard-alerts')

            <!-- Filtres temporels -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Filtres temporels</h3>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-4">
                    <!-- Filtres rapides -->
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="period" value="all"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Toutes
                        </button>
                        <button type="submit" name="period" value="today"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Aujourd'hui
                        </button>
                        <button type="submit" name="period" value="week"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Cette semaine
                        </button>
                        <button type="submit" name="period" value="month"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Ce mois
                        </button>
                        <button type="submit" name="period" value="year"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Cette année
                        </button>
                    </div>

                    <!-- Filtres personnalisés -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="mb-3">
                            <span class="text-sm text-gray-600">Période personnalisée:</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                   class="form-input text-sm">
                            <span class="text-gray-600 hidden sm:inline">à</span>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                   class="form-input text-sm">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-sm font-medium transition-colors">
                                Filtrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 md:mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 md:p-6 text-center">
                    <i class="fas fa-receipt text-2xl md:text-3xl text-blue-600 mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold text-blue-900">{{ $totalOrders }}</h3>
                    <p class="text-blue-700 text-sm md:text-base">Commandes ({{ ucfirst($period) }})</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 md:p-6 text-center">
                    <i class="fas fa-money-bill-wave text-2xl md:text-3xl text-green-600 mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold text-green-900">{{ number_format($totalRevenue, 0) }} MAD</h3>
                    <p class="text-green-700 text-sm md:text-base">Chiffre d'affaires</p>
                    <p class="text-green-600 text-xs mt-1">Commandes livrées uniquement</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 md:p-6 text-center">
                    <i class="fas fa-chart-line text-2xl md:text-3xl text-yellow-600 mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold text-yellow-900">{{ number_format($totalProfit, 0) }} MAD</h3>
                    <p class="text-yellow-700 text-sm md:text-base">Marge bénéfice</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 md:p-6 text-center">
                    <i class="fas fa-users text-2xl md:text-3xl text-purple-600 mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold text-purple-900">{{ $totalSellers }}</h3>
                    <p class="text-purple-700 text-sm md:text-base">Vendeurs actifs</p>
                </div>
            </div>

            <!-- Graphique des commandes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">Évolution des commandes (7 derniers jours)</h3>
                <div class="h-48 md:h-64">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 md:mb-8">
                <a href="{{ route('admin.products.index') }}" class="bg-white rounded-xl p-4 md:p-6 border shadow-sm hover:shadow-md transition hover:bg-gray-50">
                    <i class="fas fa-box text-2xl md:text-3xl text-blue-600 mb-3 md:mb-4"></i>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">Produits</h3>
                    <p class="text-gray-500 text-sm md:text-base">Créer, modifier et assigner des produits</p>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="bg-white rounded-xl p-4 md:p-6 border shadow-sm hover:shadow-md transition hover:bg-gray-50">
                    <i class="fas fa-folder text-2xl md:text-3xl text-orange-600 mb-3 md:mb-4"></i>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">Catégories</h3>
                    <p class="text-gray-500 text-sm md:text-base">Gérer les catégories de produits</p>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-xl p-4 md:p-6 border shadow-sm hover:shadow-md transition hover:bg-gray-50">
                    <i class="fas fa-list-check text-2xl md:text-3xl text-green-600 mb-3 md:mb-4"></i>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">Commandes</h3>
                    <p class="text-gray-500 text-sm md:text-base">Suivre et gérer les commandes</p>
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="bg-white rounded-xl p-4 md:p-6 border shadow-sm hover:shadow-md transition hover:bg-gray-50">
                    <i class="fas fa-file-invoice text-2xl md:text-3xl text-purple-600 mb-3 md:mb-4"></i>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">Facturation</h3>
                    <p class="text-gray-500 text-sm md:text-base">Synthèse des ventes et paiements vendeurs</p>
                    </a>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Données du graphique
        const chartData = @json($chartData);

        // Créer le graphique
        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(item => item.date),
                datasets: [{
                    label: 'Commandes',
                    data: chartData.map(item => item.orders),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y'
                }, {
                    label: 'Chiffre d\'affaires (MAD)',
                    data: chartData.map(item => item.revenue),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Nombre de commandes'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Chiffre d\'affaires (MAD)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    </script>
@endsection
