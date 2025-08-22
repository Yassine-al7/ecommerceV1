@extends('layouts.app')

@section('title', 'Gestion du Stock par Couleur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Gestion du Stock par Couleur</h1>
        <p class="text-gray-600">Surveillez et gérez le stock de chaque couleur de vos produits</p>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-palette text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Couleurs</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalColors">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stock Normal</p>
                    <p class="text-2xl font-bold text-gray-900" id="normalColors">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stock Faible</p>
                    <p class="text-2xl font-bold text-yellow-900" id="lowStockColors">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Rupture</p>
                    <p class="text-2xl font-bold text-red-900" id="outOfStockColors">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="flex-1">
            <form action="{{ route('admin.color-stock.search') }}" method="GET" class="flex">
                <input type="text" name="color_name" placeholder="Rechercher par couleur..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <a href="{{ route('admin.color-stock.export') }}"
           class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 flex items-center">
            <i class="fas fa-download mr-2"></i>
            Exporter CSV
        </a>
    </div>

    <!-- Produits avec couleurs en rupture -->
    @if($productsByStatus['out_of_stock']->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-red-700 flex items-center">
                <i class="fas fa-times-circle mr-2"></i>
                Couleurs en Rupture de Stock ({{ $productsByStatus['out_of_stock']->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Couleurs en Rupture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($productsByStatus['out_of_stock'] as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($product->image)
                                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ $product->image }}" alt="{{ $product->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->getOutOfStockColors() as $color)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    @if($color['hex'])
                                        <div class="w-3 h-3 rounded-full mr-1" style="background-color: {{ $color['hex'] }}"></div>
                                    @endif
                                    {{ $color['name'] }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.color-stock.show', $product) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Produits avec stock faible -->
    @if($productsByStatus['low_stock']->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-yellow-700 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Couleurs avec Stock Faible ({{ $productsByStatus['low_stock']->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Couleurs en Stock Faible</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($productsByStatus['low_stock'] as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($product->image)
                                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ $product->image }}" alt="{{ $product->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->getLowStockColors() as $color)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    @if($color['hex'])
                                        <div class="w-3 h-3 rounded-full mr-1" style="background-color: {{ $color['hex'] }}"></div>
                                    @endif
                                    {{ $color['name'] }} ({{ $color['quantity'] }})
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.color-stock.show', $product) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Produits avec stock normal -->
    @if($productsByStatus['normal']->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-green-700 flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Produits avec Stock Normal ({{ $productsByStatus['normal']->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Couleurs Disponibles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($productsByStatus['normal']->take(10) as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($product->image)
                                    <img class="h-10 w-10 rounded-lg object-cover" src="{{ $product->image }}" alt="{{ $product->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $product->category->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                @if(is_array($product->stock_couleurs))
                                    @foreach(array_slice($product->stock_couleurs, 0, 3) as $colorStock)
                                        @if(is_array($colorStock) && isset($colorStock['name']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            @if(isset($colorStock['hex']))
                                                <div class="w-3 h-3 rounded-full mr-1" style="background-color: {{ $colorStock['hex'] }}"></div>
                                            @endif
                                            {{ $colorStock['name'] }}
                                        </span>
                                        @endif
                                    @endforeach
                                    @if(count($product->stock_couleurs) > 3)
                                        <span class="text-xs text-gray-500">+{{ count($product->stock_couleurs) - 3 }} autres</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.color-stock.show', $product) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($productsByStatus['normal']->count() > 10)
        <div class="px-6 py-4 bg-gray-50 text-center">
            <span class="text-sm text-gray-600">{{ $productsByStatus['normal']->count() - 10 }} autres produits avec stock normal</span>
        </div>
        @endif
    </div>
    @endif
</div>

<script>
// Charger les statistiques au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});

function loadStatistics() {
    fetch('{{ route("admin.color-stock.statistics") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalColors').textContent = data.total_colors;
            document.getElementById('normalColors').textContent = data.total_colors - data.out_of_stock_colors - data.low_stock_colors;
            document.getElementById('lowStockColors').textContent = data.low_stock_colors;
            document.getElementById('outOfStockColors').textContent = data.out_of_stock_colors;
        })
        .catch(error => {
            console.error('Erreur lors du chargement des statistiques:', error);
        });
}
</script>
@endsection
