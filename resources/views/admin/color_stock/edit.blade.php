@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestion du Stock - {{ $product->name }}</h1>
                <a href="{{ route('admin.color_stock.show', $product) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>

            <!-- Informations du produit -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm font-medium text-blue-700">Catégorie:</span>
                        <p class="text-blue-900">{{ $product->category->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-blue-700">Stock Total:</span>
                        <p class="text-blue-900 font-semibold">{{ $product->quantite_stock ?? 0 }} unités</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-blue-700">Type:</span>
                        <p class="text-blue-900">{{ $product->isAccessory() ? 'Accessoire' : 'Produit avec tailles' }}</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.color_stock.update', $product) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Gestion des tailles (si pas un accessoire) -->
                @if(!$product->isAccessory())
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-ruler mr-2 text-blue-600"></i>Tailles disponibles
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @php
                            $commonSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '38', '40', '42', '44', '46', '48', '50', '52'];
                            $currentSizes = is_array($product->tailles) ? $product->tailles : json_decode($product->tailles, true) ?: [];
                        @endphp
                        @foreach($commonSizes as $size)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="tailles[]" value="{{ $size }}"
                                       @checked(in_array($size, $currentSizes))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm text-gray-700">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Gestion du stock par couleur -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-palette mr-2 text-blue-600"></i>Stock par couleur
                    </h3>

                    <div id="stockCouleursContainer" class="space-y-4">
                        @foreach($stockSummary as $index => $colorStock)
                        <div class="stock-color-item border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                                    <input type="text" name="stock_couleurs[{{ $index }}][name]"
                                           value="{{ $colorStock['color'] }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en stock</label>
                                    <input type="number" name="stock_couleurs[{{ $index }}][quantity]"
                                           value="{{ $colorStock['quantity'] }}" min="0" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="removeColorStock(this)"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @if($colorStock['is_low_stock'])
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Stock faible</span>
                                    @endif
                                    @if($colorStock['is_out_of_stock'])
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Rupture</span>
                                    @endif
                                </div>
                            </div>

                            @if(!$product->isAccessory() && !empty($colorStock['available_sizes']))
                            <div class="mt-3">
                                <span class="text-sm text-gray-600">Tailles disponibles: </span>
                                @foreach($colorStock['available_sizes'] as $size)
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded mr-1">{{ $size }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <button type="button" onclick="addColorStock()"
                            class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Ajouter une couleur
                    </button>
                </div>

                <!-- Résumé du stock -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">
                        <i class="fas fa-calculator mr-2"></i>Résumé du stock
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-blue-700">Nombre de couleurs:</span>
                            <p class="text-blue-900 font-semibold" id="totalColors">{{ count($stockSummary) }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-blue-700">Stock total:</span>
                            <p class="text-blue-900 font-semibold" id="totalStock">{{ $product->quantite_stock ?? 0 }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-blue-700">Statut:</span>
                            <p class="text-blue-900 font-semibold" id="stockStatus">
                                @if($product->quantite_stock > 0)
                                    <span class="text-green-600">En stock</span>
                                @else
                                    <span class="text-red-600">Rupture</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.color_stock.show', $product) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Mettre à jour le stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let colorIndex = {{ count($stockSummary) }};

function addColorStock() {
    const container = document.getElementById('stockCouleursContainer');
    const newItem = document.createElement('div');
    newItem.className = 'stock-color-item border border-gray-200 rounded-lg p-4 bg-gray-50';

    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                <input type="text" name="stock_couleurs[${colorIndex}][name]"
                       placeholder="Nom de la couleur" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en stock</label>
                <input type="number" name="stock_couleurs[${colorIndex}][quantity]"
                       value="0" min="0" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="removeColorStock(this)"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(newItem);
    colorIndex++;
    updateStockSummary();
}

function removeColorStock(button) {
    button.closest('.stock-color-item').remove();
    updateStockSummary();
}

function updateStockSummary() {
    const colorItems = document.querySelectorAll('.stock-color-item');
    const totalColors = colorItems.length;

    let totalStock = 0;
    colorItems.forEach(item => {
        const quantityInput = item.querySelector('input[name*="[quantity]"]');
        if (quantityInput) {
            totalStock += parseInt(quantityInput.value) || 0;
        }
    });

    document.getElementById('totalColors').textContent = totalColors;
    document.getElementById('totalStock').textContent = totalStock;

    const statusElement = document.getElementById('stockStatus');
    if (totalStock > 0) {
        statusElement.innerHTML = '<span class="text-green-600">En stock</span>';
    } else {
        statusElement.innerHTML = '<span class="text-red-600">Rupture</span>';
    }
}

// Mettre à jour le résumé quand les quantités changent
document.addEventListener('input', function(e) {
    if (e.target.name && e.target.name.includes('[quantity]')) {
        updateStockSummary();
    }
});

// Initialiser le résumé
document.addEventListener('DOMContentLoaded', function() {
    updateStockSummary();
});
</script>
@endsection
