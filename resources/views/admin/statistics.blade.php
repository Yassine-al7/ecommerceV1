@extends('layouts.app')

@section('title', 'الإحصائيات - لوحة الإدارة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">الإحصائيات - الرسوم البيانية</h1>

        <!-- Statistiques générales -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Chiffre d'affaires total -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">إجمالي رقم المعاملات</h3>
                <div class="text-3xl font-bold text-green-600 mb-2">
                    {{ number_format($totalRevenue, 2) }} MAD
                </div>
                <p class="text-sm text-gray-600">إجمالي الطلبات المسلمة ({{ $totalOrders }})</p>
            </div>

            <!-- Total vendeurs -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">إجمالي البائعين</h3>
                <div class="text-3xl font-bold text-blue-600 mb-2">
                    {{ $totalSellers }}
                </div>
                <p class="text-sm text-gray-600">عدد البائعين المسجلين</p>
            </div>

            <!-- Vendeurs actifs -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">البائعين النشطين</h3>
                <div class="text-3xl font-bold text-purple-600 mb-2">
                    {{ $activeSellers }}
                </div>
                <p class="text-sm text-gray-600">نشط خلال آخر 3 أشهر</p>
            </div>

            <!-- Nouveaux vendeurs ce mois -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">بائعين جدد هذا الشهر</h3>
                <div class="text-3xl font-bold text-orange-600 mb-2">
                    {{ $newSellersThisMonth }}
                </div>
                <p class="text-sm text-gray-600">مسجلين في {{ now()->format('M Y') }}</p>
            </div>
        </div>

        <!-- Graphique des top 5 produits vendus -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">أفضل 5 منتجات مبيعًا</h3>
            <div class="h-80">
                <canvas id="productsChart"></canvas>
            </div>
        </div>

        <!-- Graphique des top 6 vendeurs -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">أفضل 6 بائعين حسب المبيعات</h3>
            <div class="h-80">
                <canvas id="sellersChart"></canvas>
            </div>
        </div>

        <!-- Graphique en ligne des ventes par mois -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                📈 تطور المبيعات الفعلية (آخر 6 أشهر)
                <span class="text-sm font-normal text-gray-600 ml-2">
                    🔵 الطلبات | 💰 رقم المعاملات
                </span>
            </h3>
            <div class="h-80">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Graphique des nouveaux vendeurs par mois -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                👥 تطور البائعين الجدد (آخر 6 أشهر)
                <span class="text-sm font-normal text-gray-600 ml-2">
                    🆕 عدد البائعين المسجلين
                </span>
            </h3>
            <div class="h-80">
                <canvas id="newSellersChart"></canvas>
            </div>
        </div>

        <!-- Tableau détaillé des vendeurs -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">
                📊 تفاصيل جميع البائعين
                <span class="text-sm font-normal text-gray-600 ml-2">
                    مرتب حسب رقم المعاملات
                </span>
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">البائع</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">إجمالي الطلبات</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">طلبات مسلمة</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">رقم المعاملات</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">آخر 30 يوم</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($allSellers as $seller)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600">
                                                    {{ substr($seller->name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $seller->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $seller->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    {{ $seller->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $seller->total_orders ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $seller->delivered_orders ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <span class="font-medium text-green-600">
                                        {{ number_format($seller->total_revenue ?? 0, 2) }} MAD
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $seller->orders_last_30_days ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(($seller->orders_last_30_days ?? 0) > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            نشط
                                        </span>
                                    @elseif(($seller->total_orders ?? 0) > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            غير نشط
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            جديد
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    لا توجد بيانات للبائعين
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = { primary: '#3b82f6', secondary: '#10b981', accent: '#f59e0b', danger: '#ef4444', purple: '#8b5cf6', pink: '#ec4899' };

    // Top 5 produits vendus (commandes "livré") — dynamique, style aligné avec vendeurs
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    const productsChart = new Chart(productsCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'عدد المبيعات (طلبات مسلمة)',
                data: [],
                backgroundColor: [colors.primary, colors.secondary, colors.accent, colors.danger, colors.purple, colors.pink],
                borderColor: [colors.primary, colors.secondary, colors.accent, colors.danger, colors.purple, colors.pink],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    async function loadTopProducts() {
        try {
            const res = await fetch('{{ route('admin.statistics.top-products') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Network');
            const json = await res.json();
            productsChart.data.labels = json.labels || [];
            productsChart.data.datasets[0].data = (json.data || []).map(v => parseInt(v || 0));
            productsChart.update();
        } catch (_) { /* ignore to avoid UI break */ }
    }

    loadTopProducts();
    setInterval(loadTopProducts, 60000);
    document.addEventListener('visibilitychange', function() { if (!document.hidden) loadTopProducts(); });

    new Chart(document.getElementById('sellersChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topSellers->pluck('name')) !!},
            datasets: [{
                label: 'رقم المعاملات (MAD)',
                data: {!! json_encode($topSellers->pluck('total_revenue')) !!},
                backgroundColor: [colors.primary, colors.secondary, colors.accent, colors.danger, colors.purple, colors.pink],
                borderColor: [colors.primary, colors.secondary, colors.accent, colors.danger, colors.purple, colors.pink],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() + ' MAD' } } } }
    });

    new Chart(document.getElementById('salesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlySales->pluck('month_name')) !!},
            datasets: [{
                label: 'عدد الطلبات المسلمة',
                data: {!! json_encode($monthlySales->pluck('total_orders')) !!},
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.primary,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6
            }, {
                label: '💰 رقم المعاملات (MAD)',
                data: {!! json_encode($monthlySales->pluck('total_revenue')) !!},
                borderColor: colors.secondary,
                backgroundColor: colors.secondary + '20',
                borderWidth: 4,
                fill: false,
                tension: 0.4,
                pointBackgroundColor: colors.secondary,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 8,
                pointHoverRadius: 10,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'top', labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 12, weight: 'bold' } } } },
            scales: {
                y: { type: 'linear', display: true, position: 'left', beginAtZero: true, title: { display: true, text: 'عدد الطلبات' }, ticks: { stepSize: 1, callback: v => Math.round(v) } },
                y1: { type: 'linear', display: true, position: 'right', beginAtZero: true, title: { display: true, text: 'رقم المعاملات (MAD)' }, grid: { drawOnChartArea: false }, ticks: { callback: v => v.toLocaleString() + ' MAD' } }
            }
        }
    });

    // Graphique des nouveaux vendeurs par mois
    new Chart(document.getElementById('newSellersChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($newSellersByMonth->pluck('month_name')) !!},
            datasets: [{
                label: 'عدد البائعين الجدد',
                data: {!! json_encode($newSellersByMonth->pluck('new_sellers')) !!},
                backgroundColor: colors.purple + '80',
                borderColor: colors.purple,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Math.round(value);
                        }
                    },
                    title: {
                        display: true,
                        text: 'عدد البائعين'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'الشهر'
                    }
                }
            }
        }
    });
});
</script>
@endsection
