@extends('layouts.app')

@section('title', 'Stock par Couleur - ' . $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                <p class="text-gray-600">Gestion du stock par couleur</p>
                <div class="flex items-center mt-2">
                    <span class="text-sm text-gray-500 mr-4">Catégorie: {{ $product->category->name ?? 'N/A' }}</span>
                    <span class="text-sm text-gray-500">Statut global:
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($globalStatus === 'out_of_stock') bg-red-100 text-red-800
                            @elseif($globalStatus === 'low') bg-yellow-100 text-yellow-800
                            @elseif($globalStatus === 'medium') bg-orange-100 text-orange-800
                            @else bg-green-100 text-green-800
                            @endif">
                            @switch($globalStatus)
                                @case('out_of_stock')
                                    🔴 Rupture
                                    @break
                                @case('low')
                                    🟠 Faible
                                    @break
                                @case('medium')
                                    🟡 Moyen
                                    @break
                                @default
                                    🟢 Bon
                            @endswitch
                        </span>
                    </span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.color-stock.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Modifier le Produit
                </a>
            </div>
        </div>
    </div>

    <!-- Image du produit -->
    @if($product->image)
    <div class="mb-8">
        <img src="{{ $product->image }}" alt="{{ $product->name }}"
             class="w-32 h-32 object-cover rounded-lg shadow">
    </div>
    @endif

    <!-- Alertes -->
    @if(count($outOfStockColors) > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Couleurs en rupture de stock
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>Les couleurs suivantes ne sont plus disponibles :</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach($outOfStockColors as $color)
                        <li>{{ $color['name'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(count($lowStockColors) > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Couleurs avec stock faible
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Les couleurs suivantes ont un stock faible :</p>
                    <ul class="list-disc list-inside mt-1">
                        @foreach($lowStockColors as $color)
                        <li>{{ $color['name'] }} ({{ $color['quantity'] }} unités)</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Gestion du stock par couleur -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Gestion du Stock par Couleur</h2>
            <p class="text-sm text-gray-600 mt-1">Mettez à jour les quantités pour chaque couleur</p>
        </div>

        <div class="p-6">
            @if(is_array($colorStock) && count($colorStock) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($colorStock as $index => $color)
                        @if(is_array($color) && isset($color['name']))
                        <div class="border border-gray-200 rounded-lg p-4
                            @if(($color['quantity'] ?? 0) <= 0) bg-red-50 border-red-200
                            @elseif(($color['quantity'] ?? 0) <= 5) bg-yellow-50 border-yellow-200
                            @else bg-green-50 border-green-200
                            @endif">

                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    @if(isset($color['hex']))
                                        <div class="w-6 h-6 rounded-full mr-3 border border-gray-300"
                                             style="background-color: {{ $color['hex'] }}"></div>
                                    @endif
                                    <h3 class="font-medium text-gray-900">{{ $color['name'] }}</h3>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if(($color['quantity'] ?? 0) <= 0) bg-red-100 text-red-800
                                    @elseif(($color['quantity'] ?? 0) <= 5) bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    @if(($color['quantity'] ?? 0) <= 0)
                                        🔴 Rupture
                                    @elseif(($color['quantity'] ?? 0) <= 5)
                                        🟠 Faible
                                    @else
                                        🟢 Normal
                                    @endif
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Quantité en stock
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input type="number"
                                               name="quantity_{{ $index }}"
                                               value="{{ $color['quantity'] ?? 0 }}"
                                               min="0"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="0">
                                        <button onclick="updateColorStock('{{ $color['name'] }}', this.previousElementSibling.value)"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>

                                @if(isset($color['hex']))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Code couleur
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input type="text"
                                               value="{{ $color['hex'] }}"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50"
                                               readonly>
                                        <button onclick="copyToClipboard('{{ $color['hex'] }}')"
                                                class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif

                                <div class="text-sm text-gray-600">
                                    <p>Dernière mise à jour: {{ now()->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-palette text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">Aucune couleur configurée pour ce produit</p>
                    <p class="text-sm text-gray-400 mt-1">Ajoutez des couleurs dans la modification du produit</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Historique des modifications -->
    <div class="bg-white rounded-lg shadow mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Historique des Modifications</h2>
        </div>
        <div class="p-6">
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-history text-4xl mb-4"></i>
                <p>Historique des modifications de stock</p>
                <p class="text-sm mt-1">Cette fonctionnalité sera bientôt disponible</p>
            </div>
        </div>
    </div>
</div>

<script>
function updateColorStock(colorName, quantity) {
    if (quantity === '') {
        alert('Veuillez entrer une quantité valide');
        return;
    }

    const formData = new FormData();
    formData.append('color_name', colorName);
    formData.append('quantity', quantity);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("admin.color-stock.update", $product) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger la page pour afficher les mises à jour
            location.reload();
        } else {
            alert('Erreur lors de la mise à jour: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour du stock');
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Afficher une notification temporaire
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('bg-green-600');

        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-600');
        }, 1000);
    }).catch(function(err) {
        console.error('Erreur lors de la copie:', err);
        alert('Erreur lors de la copie du code couleur');
    });
}

// Mise à jour automatique des statistiques
setInterval(() => {
    // Ici vous pourriez ajouter une mise à jour en temps réel
}, 30000); // Toutes les 30 secondes
</script>
@endsection
