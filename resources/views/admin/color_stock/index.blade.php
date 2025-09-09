@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestion du Stock par Couleur</h1>
                <div class="flex space-x-3">
                    <button onclick="exportAllStockData()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-download mr-2"></i>Exporter tout
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>

            <!-- Statistiques globales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                @php
                    $totalProducts = $products->count();
                    $totalColors = 0;
                    $outOfStockColors = 0;
                    $lowStockColors = 0;
                    $totalStock = 0;

                    foreach($products as $product) {
                        if (is_array($product->stock_summary)) {
                            $totalColors += count($product->stock_summary);
                            foreach($product->stock_summary as $colorStock) {
                                $totalStock += $colorStock['quantity'];
                                if ($colorStock['is_out_of_stock']) {
                                    $outOfStockColors++;
                                } elseif ($colorStock['is_low_stock']) {
                                    $lowStockColors++;
                                }
                            }
                        }
                    }
                @endphp

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-box text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-600">Produits</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $totalProducts }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-palette text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-600">Couleurs</p>
                            <p class="text-2xl font-bold text-green-900">{{ $totalColors }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-600">Stock faible</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ $lowStockColors }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-600">Rupture</p>
                            <p class="text-2xl font-bold text-red-900">{{ $outOfStockColors }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                        <input type="text" id="searchInput" placeholder="Nom du produit..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                        <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes les catégories</option>
                            @php
                                $categories = $products->pluck('category.name')->unique()->filter()->sort();
                            @endphp
                            @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut du stock</label>
                        <select id="stockFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les statuts</option>
                            <option value="in_stock">En stock</option>
                            <option value="low_stock">Stock faible</option>
                            <option value="out_of_stock">Rupture</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les types</option>
                            <option value="product">Produits avec tailles</option>
                            <option value="accessory">Accessoires</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des produits -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produit
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Catégorie
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stock par couleur
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stock total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                            @foreach($products as $product)
                            <tr class="product-row hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @php
                                            $src = trim($product->image ?? '', '/');
                                            if (preg_match('#^https?://#i', $src)) {
                                                $imageUrl = $src;
                                            } elseif ($src) {
                                                $imageUrl = $product->image;
                                            } else {
                                                $imageUrl = null;
                                            }
                                        @endphp
                                        @if($imageUrl)
                                            <img class="h-10 w-10 rounded-lg object-cover mr-3" src="{{ $imageUrl }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $product->isAccessory() ? 'Accessoire' : 'Produit avec tailles' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if(is_array($product->stock_summary))
                                            @foreach(array_slice($product->stock_summary, 0, 3) as $colorStock)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    {{ $colorStock['is_out_of_stock'] ? 'bg-red-100 text-red-800' :
                                                       ($colorStock['is_low_stock'] ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                    {{ $colorStock['color'] }} ({{ $colorStock['quantity'] }})
                                                </span>
                                            @endforeach
                                            @if(count($product->stock_summary) > 3)
                                                <span class="text-xs text-gray-500">+{{ count($product->stock_summary) - 3 }} autres</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Aucun stock configuré</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $product->quantite_stock ?? 0 }}</span>
                                    <span class="text-sm text-gray-500">unités</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->quantite_stock > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            En stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rupture
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.color_stock.show', $product) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.color_stock.edit', $product) }}"
                                           class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($products->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg">Aucun produit avec stock par couleur trouvé</p>
                <p class="text-gray-400 mt-2">Commencez par ajouter des produits avec gestion de stock par couleur</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Filtrage des produits
function filterProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('categoryFilter').value;
    const stockFilter = document.getElementById('stockFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;

    const rows = document.querySelectorAll('.product-row');

    rows.forEach(row => {
        const productName = row.querySelector('td:first-child').textContent.toLowerCase();
        const category = row.querySelector('td:nth-child(2)').textContent.trim();
        const stockStatus = row.querySelector('td:nth-child(5)').textContent.trim();
        const productType = row.querySelector('td:first-child .text-gray-500').textContent.trim();

        let show = true;

        // Filtre de recherche
        if (searchTerm && !productName.includes(searchTerm)) {
            show = false;
        }

        // Filtre de catégorie
        if (categoryFilter && category !== categoryFilter) {
            show = false;
        }

        // Filtre de statut du stock
        if (stockFilter) {
            if (stockFilter === 'in_stock' && stockStatus !== 'En stock') {
                show = false;
            } else if (stockFilter === 'out_of_stock' && stockStatus !== 'Rupture') {
                show = false;
            }
        }

        // Filtre de type
        if (typeFilter) {
            if (typeFilter === 'product' && !productType.includes('tailles')) {
                show = false;
            } else if (typeFilter === 'accessory' && !productType.includes('Accessoire')) {
                show = false;
            }
        }

        row.style.display = show ? '' : 'none';
    });
}

// Événements de filtrage
document.getElementById('searchInput').addEventListener('input', filterProducts);
document.getElementById('categoryFilter').addEventListener('change', filterProducts);
document.getElementById('stockFilter').addEventListener('change', filterProducts);
document.getElementById('typeFilter').addEventListener('change', filterProducts);

// Export de toutes les données de stock
function exportAllStockData() {
    const data = {
        export_date: new Date().toISOString(),
        total_products: {{ $totalProducts }},
        total_colors: {{ $totalColors }},
        total_stock: {{ $totalStock }},
        out_of_stock_colors: {{ $outOfStockColors }},
        low_stock_colors: {{ $lowStockColors }},
        products: @json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name ?? 'N/A',
                'is_accessory' => $product->isAccessory(),
                'total_stock' => $product->quantite_stock ?? 0,
                'stock_summary' => $product->stock_summary
            ];
        }))
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'stock_complet_' + new Date().toISOString().split('T')[0] + '.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection
