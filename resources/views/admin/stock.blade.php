<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المخزون</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">إدارة المخزون</h1>
                <div class="flex space-x-3">
                    <button onclick="checkAllStocks()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        فحص كل المخزون
                    </button>
                    <button onclick="exportStockReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        تصدير تقرير
                    </button>
                </div>
            </div>

            <!-- Résumé des Alertes de Stock -->
            @php
                $lowStockCount = $products->where('quantite_stock', '<=', 5)->where('quantite_stock', '>', 0)->count();
                $outOfStockCount = $products->where('quantite_stock', '<=', 0)->count();
                $totalAlerts = $lowStockCount + $outOfStockCount;
            @endphp

            @if($totalAlerts > 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-400 text-xl mr-3"></i>
                            <div>
                                <h3 class="text-lg font-medium text-red-800">
                                    ⚠️ {{ $totalAlerts }} منتج يحتاج انتباهك
                                </h3>
                                <p class="text-red-600 mt-1">
                                    @if($outOfStockCount > 0)
                                        <span class="font-medium">{{ $outOfStockCount }} غير متوفر</span>
                                    @endif
                                    @if($lowStockCount > 0)
                                        @if($outOfStockCount > 0) و @endif
                                        <span class="font-medium">{{ $lowStockCount }} مخزون منخفض</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-red-600">آخر فحص</p>
                            <p class="text-sm font-medium text-red-800">{{ now()->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                المنتج
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                التصنيف
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                المخزون
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                سعر المشرف
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                سعر البيع
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                الحالة
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center">
                                        @if($product->image)
                                            <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ '/' . ltrim($product->image, '/') }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">
                                                @if($product->couleur)
                                                    @php
                                                        $couleurs = is_array($product->couleur) ? $product->couleur : json_decode($product->couleur, true) ?? [];
                                                        $couleurNames = [];
                                                        foreach ($couleurs as $couleur) {
                                                            if (is_array($couleur) && isset($couleur['name'])) {
                                                                $couleurNames[] = $couleur['name'];
                                                            } else {
                                                                $couleurNames[] = is_string($couleur) ? $couleur : '';
                                                            }
                                                        }
                                                    @endphp
                                                    {{ implode(', ', array_filter($couleurNames)) }}
                                                @else
                                                    لا توجد ألوان
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm text-gray-900">{{ $product->category->name ?? 'بدون تصنيف' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $product->quantite_stock }}</span>
                                        @if($product->quantite_stock <= 5)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                منخفض
                                            </span>
                                        @elseif($product->quantite_stock <= 20)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                متوسط
                                            </span>
                                        @else
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                جيد
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm text-gray-900 font-medium">{{ number_format($product->prix_admin_moyen, 2) }} MAD</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm text-gray-900 font-medium">{{ number_format((float) $product->prix_vente, 2) }} MAD</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($product->quantite_stock > 0) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($product->quantite_stock > 0)
                                            متوفر
                                        @else
                                            غير متوفر
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    لا توجد منتجات
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">إجمالي المنتجات</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $products->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">متوفر</p>
                            <p class="text-2xl font-bold text-green-900">{{ $products->where('quantite_stock', '>', 0)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">غير متوفر</p>
                            <p class="text-2xl font-bold text-red-900">{{ $products->where('quantite_stock', '<=', 0)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkAllStocks() {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>جاري الفحص...';
    button.disabled = true;
    setTimeout(() => { location.reload(); }, 2000);
}

function exportStockReport() {
    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    let csv = 'Produit,Catégorie,Stock,Prix Admin,Prix Vente,Statut\n';
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 6) {
            const productName = cells[0].textContent.trim();
            const category = cells[1].textContent.trim();
            const stock = cells[2].textContent.trim();
            const priceAdmin = cells[3].textContent.trim();
            const priceVente = cells[4].textContent.trim();
            const status = cells[5].textContent.trim();
            csv += `"${productName}","${category}","${stock}","${priceAdmin}","${priceVente}","${status}"\n`;
        }
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `rapport_stock_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Highlight alert rows on load
document.addEventListener('DOMContentLoaded', function() {
    const alertRows = document.querySelectorAll('tr');
    alertRows.forEach(row => {
        const stockCell = row.querySelector('td:nth-child(3)');
        if (stockCell) {
            const stockText = stockCell.textContent;
            if (stockText.includes('منخفض') || stockText.includes('غير متوفر')) {
                row.classList.add('bg-red-50');
            }
        }
    });
});
</script>
@endsection
</body>
</html>
