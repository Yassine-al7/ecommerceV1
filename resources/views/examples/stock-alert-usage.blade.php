{{-- Exemple d'utilisation des composants d'alerte de stock --}}

{{-- Dans votre formulaire d'édition de commande, vous pouvez utiliser ces composants comme ceci: --}}

<div class="space-y-6">
    {{-- Section des produits de la commande --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Produits de la commande</h3>

        @foreach($orderProducts as $index => $productData)
            @php
                $product = App\Models\Product::find($productData['product_id']);
            @endphp

            <div class="border border-gray-200 rounded-lg p-4 mb-4 {{ $index > 0 ? 'mt-4' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                    <span class="text-sm text-gray-500">Produit #{{ $index + 1 }}</span>
                </div>

                {{-- Informations du produit --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                        <select name="products[{{ $index }}][couleur_produit]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($product->couleur ?? [] as $couleur)
                                @php
                                    $couleurName = is_array($couleur) ? $couleur['name'] : $couleur;
                                @endphp
                                <option value="{{ $couleurName }}"
                                        {{ $productData['couleur'] === $couleurName ? 'selected' : '' }}>
                                    {{ $couleurName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Taille</label>
                        <select name="products[{{ $index }}][taille_produit]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($product->tailles ?? [] as $taille)
                                <option value="{{ $taille }}"
                                        {{ $productData['taille'] === $taille ? 'selected' : '' }}>
                                    {{ $taille }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                        <input type="number" name="products[{{ $index }}][quantite_produit]"
                               value="{{ $productData['qty'] }}" min="1" max="99"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Vérification du stock en temps réel --}}
                <div class="bg-gray-50 rounded-lg p-3">
                    <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-boxes mr-2 text-blue-600"></i>
                        Vérification du stock
                    </h5>

                    {{-- Utilisation du composant d'alerte de stock --}}
                    <x-order-product-stock-check
                        :product="$product"
                        :couleur="$productData['couleur']"
                        :taille="$productData['taille']"
                        :quantite="$productData['qty']"
                        :showDetails="true" />
                </div>

                {{-- Prix et total --}}
                <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Prix unitaire:</span> {{ number_format((float)$product->prix_vente, 2) }} MAD
                    </div>
                    <div class="text-lg font-semibold text-gray-900">
                        Total: {{ number_format((float)$product->prix_vente * $productData['qty'], 2) }} MAD
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Section des alertes globales --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
            Alertes de stock
        </h3>

        @php
            $hasAlerts = false;
            $allAlerts = [];

            foreach($orderProducts as $productData) {
                $product = App\Models\Product::find($productData['product_id']);
                $alertes = generateStockAlert(
                    $product,
                    $productData['couleur'],
                    $productData['taille'],
                    $productData['qty']
                );

                if (!empty($alertes)) {
                    $hasAlerts = true;
                    $allAlerts = array_merge($allAlerts, $alertes);
                }
            }
        @endphp

        @if($hasAlerts)
            <div class="space-y-3">
                @foreach($allAlerts as $alerte)
                    <div class="rounded-lg border p-4 {{ $alerte['type'] === 'danger' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <span class="text-2xl">{{ $alerte['icon'] }}</span>
                            </div>
                            <div class="ml-3 flex-1">
                                <h4 class="text-sm font-medium {{ $alerte['type'] === 'danger' ? 'text-red-800' : 'text-yellow-800' }}">
                                    {{ $alerte['message'] }}
                                </h4>
                                <div class="mt-2 text-sm {{ $alerte['type'] === 'danger' ? 'text-red-700' : 'text-yellow-700' }}">
                                    <p class="flex items-center">
                                        <i class="fas fa-lightbulb mr-2 text-{{ $alerte['type'] === 'danger' ? 'red' : 'yellow' }}-600"></i>
                                        {{ $alerte['solution'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Bouton d'action pour résoudre les problèmes --}}
            <div class="mt-4 flex space-x-3">
                <button type="button"
                        onclick="openStockManagement()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                    <i class="fas fa-boxes mr-2"></i>
                    Gérer le stock
                </button>

                <button type="button"
                        onclick="suggestAlternatives()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Voir les alternatives
                </button>
            </div>
        @else
            <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            Aucun problème de stock détecté
                        </p>
                        <p class="text-sm text-green-700 mt-1">
                            Tous les produits de cette commande ont un stock suffisant
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Fonctions pour gérer les actions des alertes
function openStockManagement() {
    // Ouvrir la page de gestion du stock
    window.open('/admin/stock', '_blank');
}

function suggestAlternatives() {
    // Afficher des suggestions de produits alternatifs
    alert('Fonctionnalité à implémenter: Affichage des produits alternatifs disponibles');
}

// Mise à jour en temps réel des alertes lors du changement de couleur/taille/quantité
document.addEventListener('DOMContentLoaded', function() {
    const productForms = document.querySelectorAll('[name*="[couleur_produit]"], [name*="[taille_produit]"], [name*="[quantite_produit]"]');

    productForms.forEach(input => {
        input.addEventListener('change', function() {
            // Ici vous pourriez ajouter une logique AJAX pour mettre à jour les alertes
            // sans recharger la page
            console.log('Valeur modifiée, mise à jour des alertes...');
        });
    });
});
</script>
