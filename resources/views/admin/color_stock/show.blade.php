@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Détail du Stock - {{ $product->name }}</h1>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.color_stock.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                    <a href="{{ route('admin.color_stock.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>

            <!-- Informations du produit -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-sm font-medium text-blue-700">Catégorie:</span>
                        <p class="text-blue-900">{{ $product->category->name ?? 'N/A' }}</p>
            </div>
                    <div>
                        <span class="text-sm font-medium text-blue-700">Stock Total:</span>
                        <p class="text-blue-900 font-semibold text-lg">{{ $product->quantite_stock ?? 0 }} unités</p>
        </div>
                    <div>
                        <span class="text-sm font-medium text-blue-700">Type:</span>
                        <p class="text-blue-900">{{ $product->isAccessory() ? 'Accessoire' : 'Produit avec tailles' }}</p>
    </div>
                    <div>
                        <span class="text-sm font-medium text-blue-700">Statut:</span>
                        <p class="text-blue-900">
                            @if($product->quantite_stock > 0)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded-full">En stock</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded-full">Rupture</span>
    @endif
                        </p>
            </div>
                </div>
            </div>

            <!-- Tailles disponibles (si pas un accessoire) -->
            @if(!$product->isAccessory() && !empty($product->tailles))
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-ruler mr-2 text-blue-600"></i>Tailles disponibles
                </h3>
                <div class="flex flex-wrap gap-2">
                    @php
                        $tailles = is_array($product->tailles) ? $product->tailles : json_decode($product->tailles, true) ?: [];
                    @endphp
                    @foreach($tailles as $taille)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full border border-blue-200">
                            {{ $taille }}
                        </span>
                        @endforeach
        </div>
    </div>
    @endif

            <!-- Stock par couleur -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-palette mr-2 text-blue-600"></i>Stock par couleur
                </h3>

                @if(empty($stockSummary))
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                        <p>Aucun stock par couleur configuré pour ce produit.</p>
        </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($stockSummary as $colorStock)
                        <div class="border border-gray-200 rounded-lg p-4 {{ $colorStock['is_out_of_stock'] ? 'bg-red-50 border-red-200' : ($colorStock['is_low_stock'] ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200') }}">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-lg font-semibold text-gray-800">{{ $colorStock['color'] }}</h4>
                                <div class="flex items-center space-x-2">
                                    @if($colorStock['is_out_of_stock'])
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Rupture</span>
                                    @elseif($colorStock['is_low_stock'])
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Stock faible</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">En stock</span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Quantité:</span>
                                    <p class="text-lg font-bold {{ $colorStock['is_out_of_stock'] ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $colorStock['quantity'] }} unités
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Pourcentage:</span>
                                    <p class="text-lg font-bold text-gray-800">
                                        @php
                                            $percentage = $product->quantite_stock > 0 ? round(($colorStock['quantity'] / $product->quantite_stock) * 100, 1) : 0;
                                        @endphp
                                        {{ $percentage }}%
                                    </p>
                                </div>
                            </div>

                            <!-- Barre de progression -->
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                @php
                                    $percentage = $product->quantite_stock > 0 ? ($colorStock['quantity'] / $product->quantite_stock) * 100 : 0;
                                    $barColor = $colorStock['is_out_of_stock'] ? 'bg-red-500' : ($colorStock['is_low_stock'] ? 'bg-yellow-500' : 'bg-green-500');
                                @endphp
                                <div class="h-2 rounded-full {{ $barColor }}" style="width: {{ $percentage }}%"></div>
                            </div>

                            @if(!$product->isAccessory() && !empty($colorStock['available_sizes']))
                            <div>
                                <span class="text-sm font-medium text-gray-600">Tailles disponibles:</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($colorStock['available_sizes'] as $size)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded border border-blue-200">
                                            {{ $size }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Actions rapides -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-bolt mr-2 text-blue-600"></i>Actions rapides
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="quickStockUpdate('increase')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Augmenter le stock
                    </button>
                    <button onclick="quickStockUpdate('decrease')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-colors">
                        <i class="fas fa-minus mr-2"></i>Diminuer le stock
                    </button>
                    <button onclick="exportStockData()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-download mr-2"></i>Exporter les données
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>

<!-- Modal pour la mise à jour rapide du stock -->
<div id="stockUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Mise à jour du stock</h3>
                <button onclick="closeStockModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Couleur</label>
                    <select id="modalColorSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($stockSummary as $colorStock)
                            <option value="{{ $colorStock['color'] }}">{{ $colorStock['color'] }} ({{ $colorStock['quantity'] }} unités)</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                    <input type="number" id="modalQuantity" min="1" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
        </div>
            <div class="flex justify-end space-x-3 p-4 border-t">
                <button onclick="closeStockModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Annuler
                </button>
                <button onclick="confirmStockUpdate()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAction = '';

function quickStockUpdate(action) {
    currentAction = action;
    const modal = document.getElementById('stockUpdateModal');
    const title = document.getElementById('modalTitle');

    if (action === 'increase') {
        title.textContent = 'Augmenter le stock';
    } else {
        title.textContent = 'Diminuer le stock';
    }

    modal.classList.remove('hidden');
}

function closeStockModal() {
    document.getElementById('stockUpdateModal').classList.add('hidden');
}

function confirmStockUpdate() {
    const color = document.getElementById('modalColorSelect').value;
    const quantity = parseInt(document.getElementById('modalQuantity').value);

    if (!color || !quantity || quantity < 1) {
        alert('Veuillez sélectionner une couleur et une quantité valide.');
        return;
    }

    // Appeler l'API appropriée
    const url = currentAction === 'increase'
        ? '{{ route("admin.color_stock.increase") }}'
        : '{{ route("admin.color_stock.decrease") }}';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: {{ $product->id }},
            color: color,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Stock mis à jour avec succès!');
            location.reload();
        } else {
            alert('Erreur: ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour du stock');
    });

    closeStockModal();
}

function exportStockData() {
    const data = {
        product_name: '{{ $product->name }}',
        category: '{{ $product->category->name ?? "N/A" }}',
        total_stock: {{ $product->quantite_stock ?? 0 }},
        colors: @json($stockSummary),
        export_date: new Date().toISOString()
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'stock_{{ $product->name }}_' + new Date().toISOString().split('T')[0] + '.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection
