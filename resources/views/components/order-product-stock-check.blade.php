@props(['product', 'couleur', 'taille', 'quantite', 'showDetails' => true])

@php
    // Vérifier le stock par couleur
    $stockCouleurs = $product->stock_couleurs ?: [];
    $stockCouleurTrouve = false;
    $stockDisponibleCouleur = 0;

    foreach ($stockCouleurs as $stockCouleur) {
        if (is_array($stockCouleur) && isset($stockCouleur['name']) && $stockCouleur['name'] === $couleur) {
            $stockCouleurTrouve = true;
            $stockDisponibleCouleur = $stockCouleur['quantity'] ?? 0;
            break;
        }
    }

    // Déterminer le statut du stock
    $stockStatus = 'success';
    $stockMessage = '';
    $stockIcon = '✅';

    if (!$stockCouleurTrouve) {
        $stockStatus = 'danger';
        $stockMessage = "Couleur '{$couleur}' non disponible";
        $stockIcon = '🚨';
    } elseif ($stockDisponibleCouleur <= 0) {
        $stockStatus = 'danger';
        $stockMessage = "Couleur '{$couleur}' en rupture";
        $stockIcon = '🚨';
    } elseif ($stockDisponibleCouleur < $quantite) {
        $stockStatus = 'warning';
        $stockMessage = "Stock insuffisant ({$stockDisponibleCouleur} < {$quantite})";
        $stockIcon = '⚠️';
    } else {
        $stockStatus = 'success';
        $stockMessage = "Stock disponible ({$stockDisponibleCouleur})";
        $stockIcon = '✅';
    }

    // Vérifier aussi le stock total
    if ($product->quantite_stock <= 0) {
        $stockStatus = 'danger';
        $stockMessage = "Produit en rupture totale";
        $stockIcon = '🚨';
    } elseif ($product->quantite_stock < $quantite) {
        $stockStatus = 'warning';
        $stockMessage = "Stock total insuffisant ({$product->quantite_stock} < {$quantite})";
        $stockIcon = '⚠️';
    }
@endphp

<div class="flex items-center space-x-3">
    <!-- Badge de statut du stock -->
    <div class="flex items-center">
        <span class="text-lg mr-2">{{ $stockIcon }}</span>
        <span class="px-2 py-1 text-xs font-medium rounded-full
            {{ $stockStatus === 'success' ? 'bg-green-100 text-green-800' : '' }}
            {{ $stockStatus === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
            {{ $stockStatus === 'danger' ? 'bg-red-100 text-red-800' : '' }}">
            {{ $stockMessage }}
        </span>
    </div>

    @if($showDetails)
        <!-- Informations détaillées -->
        <div class="text-sm text-gray-600">
            <span class="font-medium">Stock total:</span> {{ $product->quantite_stock }}
            @if($stockCouleurTrouve)
                <span class="mx-2">•</span>
                <span class="font-medium">Stock {$couleur}:</span> {{ $stockDisponibleCouleur }}
            @endif
        </div>

        <!-- Bouton d'aide pour plus de détails -->
        <button type="button"
                onclick="showStockDetails('{{ $product->id }}', '{{ $couleur }}', '{{ $taille }}', '{{ $quantite }}')"
                class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-info-circle"></i>
        </button>
    @endif
</div>

<!-- Modal ou popup pour les détails du stock (optionnel) -->
<div id="stockDetailsModal-{{ $product->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Détails du stock</h3>
                <button onclick="hideStockDetails('{{ $product->id }}')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div class="border-b pb-2">
                    <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                    <p class="text-sm text-gray-600">{{ $couleur }} - {{ $taille }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Stock total:</span>
                        <span class="ml-2 {{ $product->quantite_stock <= 0 ? 'text-red-600' : ($product->quantite_stock < $quantite ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $product->quantite_stock }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Quantité demandée:</span>
                        <span class="ml-2 text-gray-900">{{ $quantite }}</span>
                    </div>
                </div>

                @if($stockCouleurTrouve)
                    <div class="border-t pt-2">
                        <span class="font-medium text-gray-700">Stock couleur '{{ $couleur }}':</span>
                        <span class="ml-2 {{ $stockDisponibleCouleur <= 0 ? 'text-red-600' : ($stockDisponibleCouleur < $quantite ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $stockDisponibleCouleur }}
                        </span>
                    </div>
                @endif

                @if($stockStatus !== 'success')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-red-800">Action recommandée</h4>
                                <div class="mt-2 text-sm text-red-700">
                                    @if(!$stockCouleurTrouve)
                                        <p>• Ajouter la couleur '{{ $couleur }}' au stock</p>
                                        <p>• Ou choisir une autre couleur disponible</p>
                                    @elseif($stockDisponibleCouleur <= 0)
                                        <p>• Réapprovisionner la couleur '{{ $couleur }}'</p>
                                        <p>• Ou choisir une autre couleur</p>
                                    @elseif($stockDisponibleCouleur < $quantite)
                                        <p>• Réduire la quantité demandée</p>
                                        <p>• Ou réapprovisionner le stock</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function showStockDetails(productId, couleur, taille, quantite) {
    document.getElementById(`stockDetailsModal-${productId}`).classList.remove('hidden');
}

function hideStockDetails(productId) {
    document.getElementById(`stockDetailsModal-${productId}`).classList.add('hidden');
}
</script>
