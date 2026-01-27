@extends('layouts.app')

@section('title', __('admin_dashboard.title'))

@section('content')
    <div class="min-h-screen bg-gray-50 py-4 md:py-8">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ __('admin_dashboard.title') }}</h1>
                <p class="text-gray-600">{{ __('admin_dashboard.subtitle') }}</p>
            </div>

            <!-- Alertes de Stock -->
            @include('components.stock-dashboard-alerts')

            <!-- Filtres temporels -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">{{ __('admin_dashboard.filters.title') }}</h3>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-4">
                    <!-- Filtres rapides -->
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="period" value="all"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('admin_dashboard.filters.all') }}
                        </button>
                        <button type="submit" name="period" value="today"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('admin_dashboard.filters.today') }}
                        </button>
                        <button type="submit" name="period" value="week"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('admin_dashboard.filters.week') }}
                        </button>
                        <button type="submit" name="period" value="month"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('admin_dashboard.filters.month') }}
                        </button>
                        <button type="submit" name="period" value="year"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ __('admin_dashboard.filters.year') }}
                        </button>
                    </div>

                    <!-- Filtres personnalisés -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="mb-3">
                            <span class="text-sm text-gray-600">{{ __('admin_dashboard.filters.custom') }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                   class="form-input text-sm">
                            <span class="text-gray-600 hidden sm:inline">{{ __('admin_dashboard.filters.to') }}</span>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                   class="form-input text-sm">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg text-sm font-medium transition-colors">
                                {{ __('admin_dashboard.filters.apply') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 md:mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 md:p-6 text-center">
                    <i class="fas fa-receipt text-2xl md:text-3xl text-blue-600 mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold text-blue-900">{{ $totalOrders }}</h3>
                    <p class="text-blue-700 text-sm md:text-base">{{ __('admin_dashboard.stats.orders', ['period' => __($period)]) }}</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 md:p-6 text-center">
                    <i class="fas fa-money-bill-wave text-2xl md:text-3xl text-green-600 mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold text-green-900">{{ number_format($totalRevenue, 0) }} MAD</h3>
                    <p class="text-green-700 text-sm md:text-base">{{ __('admin_dashboard.stats.revenue') }}</p>
                    <p class="text-green-600 text-xs mt-1">{{ __('admin_dashboard.stats.revenue_hint') }}</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 md:p-6 text-center">
                    <i class="fas fa-chart-line text-2xl md:text-3xl text-yellow-600 mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold text-yellow-900">{{ number_format($totalProfit, 0) }} MAD</h3>
                    <p class="text-yellow-700 text-sm md:text-base">{{ __('admin_dashboard.stats.profit') }}</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 md:p-6 text-center">
                    <i class="fas fa-users text-2xl md:text-3xl text-purple-600 mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold text-purple-900">{{ $totalSellers }}</h3>
                    <p class="text-purple-700 text-sm md:text-base">{{ __('admin_dashboard.stats.active_sellers') }}</p>
                </div>
            </div>

            <!-- Graphique des commandes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-4">{{ __('admin_dashboard.charts.orders_7d') }}</h3>
                <div class="h-48 md:h-64">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 md:mb-8">
                <a href="{{ route('admin.products.index') }}" class="bg-white rounded-xl p-4 md:p-6 border shadow-sm hover:shadow-md transition hover:bg-gray-50">
                    <i class="fas fa-box text-2xl md:text-3xl text-blue-600 mb-3 md:mb-4"></i>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">{{ __('admin_dashboard.quick.products.title') }}</h3>
                    <p class="text-gray-500 text-sm md:text-base">{{ __('admin_dashboard.quick.products.desc') }}</p>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="bg-white rounded-xl p-4 md:p-6 border shadow-sm hover:shadow-md transition hover:bg-gray-50">
                    <i class="fas fa-folder text-2xl md:text-3xl text-orange-600 mb-3 md:mb-4"></i>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">{{ __('admin_dashboard.quick.categories.title') }}</h3>
                    <p class="text-gray-500 text-sm md:text-base">{{ __('admin_dashboard.quick.categories.desc') }}</p>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-xl p-4 md:p-6 border shadow-sm hover:shadow-md transition hover:bg-gray-50">
                    <i class="fas fa-list-check text-2xl md:text-3xl text-green-600 mb-3 md:mb-4"></i>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">{{ __('admin_dashboard.quick.orders.title') }}</h3>
                    <p class="text-gray-500 text-sm md:text-base">{{ __('admin_dashboard.quick.orders.desc') }}</p>
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="bg-white rounded-xl p-4 md:p-6 border shadow-sm hover:shadow-md transition hover:bg-gray-50">
                    <i class="fas fa-file-invoice text-2xl md:text-3xl text-purple-600 mb-3 md:mb-4"></i>
                    <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-1">{{ __('admin_dashboard.quick.invoices.title') }}</h3>
                    <p class="text-gray-500 text-sm md:text-base">{{ __('admin_dashboard.quick.invoices.desc') }}</p>
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
                    label: '{{ __('admin_dashboard.charts.orders_label') }}',
                    data: chartData.map(item => item.orders),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y'
                }, {
                    label: '{{ __('admin_dashboard.charts.revenue_label') }}',
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
                            text: '{{ __('admin_dashboard.charts.y_orders') }}'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: '{{ __('admin_dashboard.charts.y_revenue') }}'
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
