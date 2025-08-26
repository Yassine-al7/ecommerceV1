@extends('layouts.app')

@section('title', 'الإحصائيات - لوحة الإدارة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">الإحصائيات - الرسوم البيانية</h1>

        <!-- Chiffre d'affaires total des commandes livrées -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">إجمالي رقم المعاملات</h3>
            <div class="text-4xl font-bold text-green-600 mb-2">
                {{ number_format($topSellers->sum('total_revenue'), 2) }} MAD
            </div>
            <p class="text-sm text-gray-600">إجمالي الطلبات المسلمة</p>
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
    </div>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = { primary: '#3b82f6', secondary: '#10b981', accent: '#f59e0b', danger: '#ef4444', purple: '#8b5cf6', pink: '#ec4899' };

    new Chart(document.getElementById('productsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topProducts->pluck('name')) !!},
            datasets: [{
                label: 'عدد المبيعات',
                data: {!! json_encode($topProducts->pluck('total_sales')) !!},
                backgroundColor: [colors.primary, colors.secondary, colors.accent, colors.purple, colors.pink],
                borderColor: [colors.primary, colors.secondary, colors.accent, colors.purple, colors.pink],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

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
});
</script>
@endsection
