@extends('layouts.app')

@section('title', isset($order) ? 'Modifier Commande' : 'Créer Commande')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ isset($order) ? 'Modifier la commande' : 'Créer une nouvelle commande' }}
                    </h1>
                    <p class="text-gray-600 mt-2">
                        {{ isset($order) ? "Référence: {$order->reference}" : 'Remplissez les informations ci-dessous' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.orders.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                    @if(isset($order))
                    <a href="{{ route('admin.orders.show', $order->id) }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-eye mr-2"></i>Voir
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Formulaire principal -->
        <form method="POST" action="{{ isset($order) ? route('admin.orders.update', $order) : route('admin.orders.store') }}"
              class="space-y-8" id="orderForm">
            @csrf
            @if(isset($order))
                @method('PUT')
            @endif

            <!-- Informations de base -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Informations de base
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Référence -->
                    @if(isset($order))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Référence</label>
                        <input type="text" value="{{ $order->reference }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600" readonly>
                        <p class="text-xs text-gray-500 mt-1">La référence ne peut pas être modifiée</p>
                    </div>
                    @endif

                    <!-- Nom client -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom client *</label>
                        <input type="text" name="nom_client"
                               value="{{ old('nom_client', $order->nom_client ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('nom_client')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ville -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                        <select name="ville" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Sélectionner une ville</option>
                            @foreach(config('delivery.cities', []) as $cityCode => $cityData)
                                <option value="{{ $cityCode }}" @selected(old('ville', $order->ville ?? '') == $cityCode)>
                                    {{ $cityData['name'] ?? $cityCode }} - {{ $cityData['price'] ?? 0 }} MAD
                                </option>
                            @endforeach
                        </select>
                        @error('ville')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Adresse client -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adresse complète *</label>
                        <textarea name="adresse_client" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  required>{{ old('adresse_client', $order->adresse_client ?? '') }}</textarea>
                        @error('adresse_client')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                        <input type="tel" name="numero_telephone_client"
                               value="{{ old('numero_telephone_client', $order->numero_telephone_client ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('numero_telephone_client')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vendeur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vendeur *</label>
                        <select name="seller_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Sélectionner un vendeur</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" @selected(old('seller_id', $order->seller_id ?? '') == $seller->id)>
                                    {{ $seller->name }} ({{ $seller->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('seller_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach(['en attente', 'non confirmé', 'confirme', 'en livraison', 'livre', 'pas de réponse', 'annulé', 'retourné'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $order->status ?? 'en attente') == $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Gestion des produits -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-box text-green-500 mr-2"></i>
                    Produits de la commande
                </h2>

                <!-- Bouton d'ajout de produit -->
                <div class="mb-4">
                    <button type="button" onclick="addProductRow()"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Ajouter un produit
                    </button>
                </div>

                <!-- Tableau des produits -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Couleur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taille</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            @if(isset($order) && isset($orderProducts))
                                @foreach($orderProducts as $index => $productData)
                                    <tr class="product-row border-b border-gray-200" data-index="{{ $index }}">
                                        <td class="px-4 py-3">
                                            <select name="products[{{ $index }}][product_id]"
                                                    class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    onchange="updateProductInfo({{ $index }})" required>
                                                <option value="">Sélectionner un produit</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}"
                                                            data-prix="{{ $product->prix_vente }}"
                                                            data-stock="{{ $product->quantite_stock }}"
                                                            data-couleurs="{{ json_encode($product->couleur) }}"
                                                            data-tailles="{{ json_encode($product->tailles) }}"
                                                            @selected($productData['product_id'] == $product->id)>
                                                        {{ $product->name }} - {{ $product->prix_vente }} MAD
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select name="products[{{ $index }}][couleur_produit]"
                                                    class="couleur-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                                <option value="">Sélectionner une couleur</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <select name="products[{{ $index }}][taille_produit]"
                                                    class="taille-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Sélectionner une taille</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="products[{{ $index }}][quantite_produit]"
                                                   value="{{ $productData['qty'] ?? 1 }}" min="1"
                                                   class="quantite-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   onchange="updateProductTotal({{ $index }})" required>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="products[{{ $index }}][prix_vente_client]"
                                                   value="{{ $productData['prix_vente_client'] ?? 0 }}" step="0.01" min="0"
                                                   class="prix-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   onchange="updateProductTotal({{ $index }})" required>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="prix-total font-semibold text-gray-900">0.00</span> MAD
                                        </td>
                                        <td class="px-4 py-3">
                                            <button type="button" onclick="removeProductRow({{ $index }})"
                                                    class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <!-- Ligne par défaut pour nouvelle commande -->
                                <tr class="product-row border-b border-gray-200" data-index="0">
                                    <td class="px-4 py-3">
                                        <select name="products[0][product_id]"
                                                class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                onchange="updateProductInfo(0)" required>
                                            <option value="">Sélectionner un produit</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-prix="{{ $product->prix_vente }}"
                                                        data-stock="{{ $product->quantite_stock }}"
                                                        data-couleurs="{{ json_encode($product->couleur) }}"
                                                        data-tailles="{{ json_encode($product->tailles) }}"
                                                        data-stock-couleurs="{{ json_encode($product->stock_couleurs) }}">
                                                    {{ $product->name }} - {{ $product->prix_vente }} MAD
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select name="products[0][couleur_produit]"
                                                class="couleur-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                            <option value="">Sélectionner une couleur</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select name="products[0][taille_produit]"
                                                class="taille-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Sélectionner une taille</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="products[0][quantite_produit]"
                                               value="1" min="1"
                                               class="quantite-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               onchange="updateProductTotal(0)" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" name="products[0][prix_vente_client]"
                                               value="0" step="0.01" min="0"
                                               class="prix-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               onchange="updateProductTotal(0)" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="prix-total font-semibold text-gray-900">0.00</span> MAD
                                    </td>
                                    <td class="px-4 py-3">
                                        <button type="button" onclick="removeProductRow(0)"
                                                class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Note explicative sur le stock -->
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Information sur le stock</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p class="mb-2">• <strong>Seules les couleurs en stock sont affichées</strong> dans la liste déroulante</p>
                                <p class="mb-2">• <strong>Si une couleur ne s'affiche pas</strong>, cela signifie qu'elle n'est pas en stock</p>
                                <p class="mb-2">• <strong>Le stock est vérifié en temps réel</strong> pour chaque couleur sélectionnée</p>
                                <p>• <strong>Les couleurs en rupture de stock</strong> sont automatiquement masquées</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Résumé et calculs -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calculator text-purple-500 mr-2"></i>
                    Résumé de la commande
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Calculs automatiques -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Sous-total produits:</span>
                            <span class="font-semibold text-gray-900" id="subtotal">0.00 MAD</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Frais de livraison:</span>
                            <span class="font-semibold text-gray-900" id="livraison">0.00 MAD</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600">Total commande:</span>
                            <span class="font-semibold text-xl text-blue-600" id="totalCommande">0.00 MAD</span>
                        </div>
                    </div>

                    <!-- Informations supplémentaires -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                            <textarea name="commentaire" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Commentaires sur la commande...">{{ old('commentaire', $order->commentaire ?? '') }}</textarea>
                        </div>

                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="notifyClient" name="notify_client" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="notifyClient" class="text-sm text-gray-700">Notifier le client par email</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="window.history.back()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    {{ isset($order) ? 'Mettre à jour' : 'Créer' }} la commande
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let productIndex = {{ isset($orderProducts) ? count($orderProducts) : 1 }};

// Ajouter une nouvelle ligne de produit
function addProductRow() {
    const tbody = document.getElementById('productsTableBody');
    const newRow = document.createElement('tr');
    newRow.className = 'product-row border-b border-gray-200';
    newRow.setAttribute('data-index', productIndex);

    newRow.innerHTML = `
        <td class="px-4 py-3">
            <select name="products[${productIndex}][product_id]"
                    class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    onchange="updateProductInfo(${productIndex})" required>
                <option value="">Sélectionner un produit</option>
                                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                        data-prix="{{ $product->prix_vente }}"
                                                        data-stock="{{ $product->quantite_stock }}"
                                                        data-couleurs="{{ json_encode($product->couleur) }}"
                                                        data-tailles="{{ json_encode($product->tailles) }}"
                                                        data-stock-couleurs="{{ json_encode($product->stock_couleurs) }}">
                                                    {{ $product->name }} - {{ $product->prix_vente }} MAD
                                                </option>
                                            @endforeach
            </select>
        </td>
        <td class="px-4 py-3">
            <select name="products[${productIndex}][couleur_produit]"
                    class="couleur-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Sélectionner une couleur</option>
            </select>
        </td>
        <td class="px-4 py-3">
            <select name="products[${productIndex}][taille_produit]"
                    class="taille-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Sélectionner une taille</option>
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="number" name="products[${productIndex}][quantite_produit]"
                   value="1" min="1"
                   class="quantite-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   onchange="updateProductTotal(${productIndex})" required>
        </td>
        <td class="px-4 py-3">
            <input type="number" name="products[${productIndex}][prix_vente_client]"
                   value="0" step="0.01" min="0"
                   class="prix-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   onchange="updateProductTotal(${productIndex})" required>
        </td>
        <td class="px-4 py-3">
            <span class="prix-total font-semibold text-gray-900">0.00</span> MAD
        </td>
        <td class="px-4 py-3">
            <button type="button" onclick="removeProductRow(${productIndex})"
                    class="text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(newRow);
    productIndex++;
}

// Supprimer une ligne de produit
function removeProductRow(index) {
    const row = document.querySelector(`tr[data-index="${index}"]`);
    if (row) {
        row.remove();
        updateOrderTotal();
    }
}

// Mettre à jour les informations du produit
function updateProductInfo(index) {
    const row = document.querySelector(`tr[data-index="${index}"]`);
    if (!row) {
        console.error(`❌ Ligne avec index ${index} non trouvée dans updateProductInfo`);
        return;
    }

    const productSelect = row.querySelector('.product-select');
    if (!productSelect) {
        console.error(`❌ Sélecteur de produit non trouvé pour l'index ${index}`);
        return;
    }

    const selectedOption = productSelect.options[productSelect.selectedIndex];
    if (!selectedOption) {
        console.error(`❌ Option sélectionnée non trouvée pour l'index ${index}`);
        return;
    }

    if (selectedOption.value) {
        const prix = parseFloat(selectedOption.getAttribute('data-prix'));
        const stock = parseInt(selectedOption.getAttribute('data-stock'));
        const couleurs = JSON.parse(selectedOption.getAttribute('data-couleurs'));
        const tailles = JSON.parse(selectedOption.getAttribute('data-tailles'));

        // Mettre à jour le prix
        const prixInput = row.querySelector('.prix-input');
        if (prixInput) {
            prixInput.value = prix;
        } else {
            console.error(`❌ Input prix non trouvé pour l'index ${index}`);
        }

        // Ne pas afficher le stock total par défaut - attendre la sélection de couleur
        const stockDisplay = row.querySelector('.stock-display');
        if (stockDisplay) {
            stockDisplay.textContent = '-';
            stockDisplay.className = 'stock-display text-sm text-gray-600';
        } else {
            console.error(`❌ Affichage stock non trouvé pour l'index ${index}`);
        }

                // Mettre à jour les couleurs en filtrant celles sans stock
        const couleurSelect = row.querySelector('.couleur-select');
        if (!couleurSelect) {
            console.error(`❌ Sélecteur de couleur non trouvé pour l'index ${index}`);
            return;
        }
        couleurSelect.innerHTML = '<option value="">Sélectionner une couleur</option>';

                // Récupérer les stocks par couleur
        let couleursDisponibles = [];
        let stockCouleurs = null;

        // Essayer de récupérer le stock par couleur depuis l'attribut data
        if (selectedOption.getAttribute('data-stock-couleurs')) {
            try {
                stockCouleurs = JSON.parse(selectedOption.getAttribute('data-stock-couleurs'));
                console.log('Stock couleurs parsé:', stockCouleurs);
            } catch (e) {
                console.error('Erreur parsing stock_couleurs:', e);
                stockCouleurs = null;
            }
        }

                        // Traiter chaque couleur
        couleurs.forEach(couleur => {
            const couleurName = typeof couleur === 'string' ? couleur : couleur.name;
            let stockCouleur = 0;
            let couleurTrouvee = false;

            console.log(`🔍 Traitement de la couleur: ${couleurName}`);
            console.log(`📊 Stock couleurs disponible:`, stockCouleurs);
            console.log(`📊 Type de stockCouleurs:`, typeof stockCouleurs);
            console.log(`📊 Est un tableau:`, Array.isArray(stockCouleurs));

            // Chercher le stock pour cette couleur dans stock_couleurs
            if (stockCouleurs && Array.isArray(stockCouleurs)) {
                console.log(`🔍 Recherche dans ${stockCouleurs.length} éléments de stock`);

                stockCouleurs.forEach((stockData, index) => {
                    console.log(`  Vérification stockData[${index}]:`, stockData);
                    console.log(`  Type de stockData:`, typeof stockData);
                    console.log(`  Est un objet:`, stockData !== null && typeof stockData === 'object');

                    if (stockData && typeof stockData === 'object') {
                        console.log(`  Propriétés de stockData:`, Object.keys(stockData));
                        console.log(`  stockData.name:`, stockData.name);
                        console.log(`  stockData.quantity:`, stockData.quantity);

                        if (stockData.name !== undefined) {
                            console.log(`  Type de stockData.name:`, typeof stockData.name, `Valeur:`, JSON.stringify(stockData.name));
                            console.log(`  Type de couleurName:`, typeof couleurName, `Valeur:`, JSON.stringify(couleurName));

                            // Comparaison stricte avec conversion de type
                            const stockName = String(stockData.name).trim();
                            const couleurNameTrim = String(couleurName).trim();

                            console.log(`  Comparaison: "${stockName}" === "${couleurNameTrim}" ?`);
                            console.log(`  Longueur stockName:`, stockName.length);
                            console.log(`  Longueur couleurNameTrim:`, couleurNameTrim.length);
                            console.log(`  Codes ASCII stockName:`, Array.from(stockName).map(c => c.charCodeAt(0)));
                            console.log(`  Codes ASCII couleurNameTrim:`, Array.from(couleurNameTrim).map(c => c.charCodeAt(0)));

                            if (stockName === couleurNameTrim) {
                                stockCouleur = parseInt(stockData.quantity) || 0;
                                couleurTrouvee = true;
                                console.log(`✅ Stock trouvé pour ${couleurName}: ${stockCouleur}`);
                            } else {
                                console.log(`  ❌ "${stockName}" !== "${couleurNameTrim}"`);
                            }
                        } else {
                            console.log(`  ⚠️ stockData.name est undefined`);
                        }
                    } else {
                        console.log(`  ⚠️ stockData n'est pas un objet valide:`, stockData);
                    }
                });
            } else {
                console.log(`⚠️ stockCouleurs n'est pas un tableau valide:`, stockCouleurs);
            }

            // Si la couleur a du stock, l'ajouter à la liste
            if (couleurTrouvee && stockCouleur > 0) {
                couleursDisponibles.push({
                    name: couleurName,
                    stock: stockCouleur
                });
                console.log(`✅ Couleur ajoutée: ${couleurName} avec stock ${stockCouleur}`);
            } else if (couleurTrouvee && stockCouleur === 0) {
                console.log(`❌ Couleur ${couleurName} masquée (stock = 0)`);
            } else {
                console.log(`⚠️ Couleur ${couleurName} non trouvée dans stock_couleurs`);
                // En cas d'erreur, afficher la couleur avec le stock total comme fallback
                couleursDisponibles.push({
                    name: couleurName,
                    stock: stock
                });
                console.log(`🔄 Fallback: ${couleurName} avec stock total ${stock}`);
            }
        });

        // Si aucune couleur n'a de stock défini, afficher toutes avec le stock total
        if (couleursDisponibles.length === 0) {
            console.log('⚠️ Aucune couleur avec stock défini, utilisation du stock total comme fallback');
            couleurs.forEach(couleur => {
                const couleurName = typeof couleur === 'string' ? couleur : couleur.name;
                couleursDisponibles.push({
                    name: couleurName,
                    stock: stock
                });
                console.log(`🔄 Fallback: ${couleurName} avec stock total ${stock}`);
            });
        } else {
            console.log(`✅ ${couleursDisponibles.length} couleurs avec stock spécifique trouvées`);
        }

        // Ajouter aussi les couleurs sans stock pour les afficher avec "Rupture de stock"
        couleurs.forEach(couleur => {
            const couleurName = typeof couleur === 'string' ? couleur : couleur.name;

            // Vérifier si cette couleur est déjà dans couleursDisponibles
            const dejaPresente = couleursDisponibles.find(c => c.name === couleurName);
            if (!dejaPresente) {
                couleursDisponibles.push({
                    name: couleurName,
                    stock: 0
                });
                console.log(`❌ Couleur ajoutée en rupture: ${couleurName}`);
            }
        });

                        // Afficher les couleurs disponibles avec leur stock
        couleursDisponibles.forEach(couleurData => {
            const option = document.createElement('option');
            option.value = couleurData.name;

            // Afficher le stock de manière claire
            if (couleurData.stock === 'N/A') {
                option.textContent = `${couleurData.name} (Stock: N/A)`;
                option.setAttribute('data-stock', 'N/A');
            } else if (couleurData.stock === 0) {
                option.textContent = `${couleurData.name} (Rupture de stock)`;
                option.setAttribute('data-stock', '0');
                option.disabled = true; // Désactiver l'option en rupture
                option.style.color = '#999'; // Griser l'option
            } else {
                option.textContent = `${couleurData.name} (Stock: ${couleurData.stock})`;
                option.setAttribute('data-stock', couleurData.stock);
            }

            console.log(`🎨 Option créée: ${couleurData.name} avec data-stock="${couleurData.stock}"`);
            couleurSelect.appendChild(option);
        });

        // Debug: vérifier les options créées
        console.log('📋 Options de couleur créées:');
        Array.from(couleurSelect.options).forEach((opt, index) => {
            console.log(`  [${index}] ${opt.textContent} - data-stock="${opt.getAttribute('data-stock')}"`);
        });

        // Debug: afficher les informations dans la console
        console.log('Produit sélectionné:', selectedOption.textContent);
        console.log('Stock total:', stock);
        console.log('Couleurs disponibles:', couleursDisponibles);

                // Mettre à jour les tailles
        const tailleSelect = row.querySelector('.taille-select');
        if (tailleSelect) {
            tailleSelect.innerHTML = '<option value="">Sélectionner une taille</option>';
            tailles.forEach(taille => {
                const option = document.createElement('option');
                option.value = taille;
                option.textContent = taille;
                tailleSelect.appendChild(option);
            });
        } else {
            console.error(`❌ Sélecteur de taille non trouvé pour l'index ${index}`);
        }

        // Mettre à jour le total
        updateProductTotal(index);
    }
}

// Fonction pour calculer le stock réel disponible
function calculateRealStock(productId, couleur, taille) {
    const productSelect = document.querySelector(`select[data-product-id="${productId}"]`);
    if (!productSelect) return 0;

    const selectedOption = productSelect.options[productSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) return 0;

    const stockCouleurs = selectedOption.getAttribute('data-stock-couleurs');
    if (!stockCouleurs) return 0;

    try {
        const stockCouleursData = JSON.parse(stockCouleurs);
        let stockDisponible = 0;

        // Chercher le stock pour la couleur spécifique
        stockCouleursData.forEach(stockData => {
            if (stockData.name === couleur) {
                stockDisponible = parseInt(stockData.quantity) || 0;
            }
        });

        return stockDisponible;
    } catch (e) {
        console.error('Erreur parsing stock_couleurs:', e);
        return 0;
    }
}

// Fonction pour mettre à jour les alertes de stock
function updateStockAlerts(index) {
    const row = document.querySelector(`tr[data-index="${index}"]`);
    if (!row) {
        console.error(`❌ Ligne avec index ${index} non trouvée`);
        return;
    }

    const productSelect = row.querySelector('.product-select');
    const couleurSelect = row.querySelector('.couleur-select');
    const tailleSelect = row.querySelector('.taille-select');
    const quantiteInput = row.querySelector('.quantite-input');
    const alertsContainer = row.querySelector('.stock-alerts-container');
    const stockDisplay = row.querySelector('.stock-display');

    // Vérifier que tous les éléments nécessaires existent
    if (!productSelect || !couleurSelect || !quantiteInput || !alertsContainer || !stockDisplay) {
        console.error('❌ Éléments DOM manquants dans updateStockAlerts:', {
            productSelect: !!productSelect,
            couleurSelect: !!couleurSelect,
            quantiteInput: !!quantiteInput,
            alertsContainer: !!alertsContainer,
            stockDisplay: !!stockDisplay
        });
        return;
    }

    if (!productSelect.value || !couleurSelect.value || !quantiteInput.value) {
        if (alertsContainer) alertsContainer.innerHTML = '';
        if (stockDisplay) {
            stockDisplay.textContent = '-';
            stockDisplay.className = 'stock-display text-sm text-gray-600';
        }
        return;
    }

    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const stockTotal = parseInt(selectedOption.getAttribute('data-stock'));
    const couleurs = JSON.parse(selectedOption.getAttribute('data-couleurs'));
    const tailles = JSON.parse(selectedOption.getAttribute('data-tailles'));

    const couleur = couleurSelect.value;
    const taille = tailleSelect.value;
    const quantite = parseInt(quantiteInput.value) || 0;

        // Calculer le stock réel disponible pour cette couleur
    let stockCouleur = 0;
    let couleurTrouvee = false;

            // Récupérer le stock depuis l'option de couleur sélectionnée
    const couleurOption = couleurSelect.options[couleurSelect.selectedIndex];
    if (couleurOption && couleurOption.getAttribute('data-stock')) {
        const stockFromOption = couleurOption.getAttribute('data-stock');
        console.log(`🔍 Stock depuis l'option: ${stockFromOption} pour ${couleur}`);

        if (stockFromOption !== 'N/A' && stockFromOption !== undefined) {
            stockCouleur = parseInt(stockFromOption) || 0;
            couleurTrouvee = true;
            console.log(`✅ Stock récupéré depuis l'option: ${couleur} = ${stockCouleur}`);
        }
    }

    // Si pas de stock dans l'option, essayer de le récupérer depuis stock_couleurs
    if (!couleurTrouvee && selectedOption.getAttribute('data-stock-couleurs')) {
        try {
            const stockCouleurs = JSON.parse(selectedOption.getAttribute('data-stock-couleurs'));
            console.log('🔍 Recherche du stock pour la couleur:', couleur, 'dans:', stockCouleurs);

                        stockCouleurs.forEach((stockCouleurData, index) => {
                console.log(`  Vérification [${index}]: ${stockCouleurData.name} === ${couleur} ?`);
                console.log(`  Type de stockCouleurData.name:`, typeof stockCouleurData.name, `Valeur:`, JSON.stringify(stockCouleurData.name));
                console.log(`  Type de couleur:`, typeof couleur, `Valeur:`, JSON.stringify(couleur));

                if (stockCouleurData && stockCouleurData.name) {
                    // Comparaison stricte avec conversion de type
                    const stockName = String(stockCouleurData.name).trim();
                    const couleurTrim = String(couleur).trim();

                    console.log(`  Comparaison: "${stockName}" === "${couleurTrim}" ?`);

                    if (stockName === couleurTrim) {
                        stockCouleur = parseInt(stockCouleurData.quantity) || 0;
                        couleurTrouvee = true;
                        console.log(`✅ Stock trouvé dans stock_couleurs: ${couleur} = ${stockCouleur}`);
                    } else {
                        console.log(`  ❌ "${stockName}" !== "${couleurTrim}"`);
                    }
                } else {
                    console.log(`  ⚠️ stockCouleurData ou stockCouleurData.name invalide:`, stockCouleurData);
                }
            });
        } catch (e) {
            console.error('❌ Erreur parsing stock_couleurs:', e);
        }
    }

    // Si toujours pas trouvé, essayer de récupérer depuis les couleurs disponibles
    if (!couleurTrouvee) {
        console.log('🔄 Tentative de récupération depuis les couleurs disponibles...');
        const couleursDisponibles = Array.from(couleurSelect.options).map(opt => ({
            name: opt.value,
            stock: opt.getAttribute('data-stock')
        }));

        console.log('📋 Couleurs disponibles:', couleursDisponibles);

        const couleurDisponible = couleursDisponibles.find(c => c.name === couleur);
        if (couleurDisponible && couleurDisponible.stock !== 'N/A') {
            stockCouleur = parseInt(couleurDisponible.stock) || 0;
            couleurTrouvee = true;
            console.log(`✅ Stock récupéré depuis les couleurs disponibles: ${couleur} = ${stockCouleur}`);
        }
    }

    // Debug: afficher les informations
    console.log('Résultat de la recherche de stock:', {
        couleur: couleur,
        stockCouleur: stockCouleur,
        couleurTrouvee: couleurTrouvee,
        stockTotal: stockTotal
    });

    // Mettre à jour l'affichage du stock avec le stock réel
    if (couleurTrouvee) {
        stockDisplay.textContent = stockCouleur;
        if (stockCouleur <= 0) {
            stockDisplay.className = 'stock-display text-sm text-red-600 font-semibold';
        } else if (stockCouleur < quantite) {
            stockDisplay.className = 'stock-display text-sm text-yellow-600 font-semibold';
        } else {
            stockDisplay.className = 'stock-display text-sm text-green-600 font-semibold';
        }
    } else {
        // Si pas de stock par couleur, afficher le stock total
        stockDisplay.textContent = stockTotal;
        if (stockTotal <= 0) {
            stockDisplay.className = 'stock-display text-sm text-red-600 font-semibold';
        } else if (stockTotal < quantite) {
            stockDisplay.className = 'stock-display text-sm text-yellow-600 font-semibold';
        } else {
            stockDisplay.className = 'stock-display text-sm text-green-600 font-semibold';
        }
    }

        // Générer les alertes
    let alertes = [];

    // Vérifier le stock de la couleur sélectionnée
    if (couleurTrouvee) {
        if (stockCouleur <= 0) {
            alertes.push({
                type: 'danger',
                message: `Couleur '${couleur}' en rupture de stock`,
                icon: '🚨'
            });
        } else if (stockCouleur < quantite) {
            alertes.push({
                type: 'warning',
                message: `Stock couleur '${couleur}' insuffisant (${stockCouleur} < ${quantite})`,
                icon: '⚠️'
            });
        } else {
            alertes.push({
                type: 'success',
                message: `Stock couleur '${couleur}' suffisant (${stockCouleur} disponible)`,
                icon: '✅'
            });
        }
    } else {
        // Si la couleur n'est pas trouvée, c'est qu'elle n'a pas de stock
        alertes.push({
            type: 'danger',
            message: `Couleur '${couleur}' non disponible ou sans stock`,
            icon: '🚨'
        });
    }

    // Vérifier le stock total seulement si nécessaire
    if (stockTotal <= 0) {
        alertes.push({
            type: 'danger',
            message: 'Produit en rupture totale',
            icon: '🚨'
        });
    } else if (couleurTrouvee && stockCouleur < quantite && stockTotal < quantite) {
        // Afficher l'alerte de stock total seulement si la couleur n'a pas assez ET le total non plus
        alertes.push({
            type: 'warning',
            message: `Stock total insuffisant (${stockTotal} < ${quantite})`,
            icon: '⚠️'
        });
    }

    // Afficher les alertes
    if (alertes.length > 0) {
        let alertsHTML = '';
        alertes.forEach(alerte => {
            const bgColor = alerte.type === 'danger' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200';
            const textColor = alerte.type === 'danger' ? 'text-red-800' : 'text-yellow-800';

            alertsHTML += `
                <div class="rounded border p-2 ${bgColor} mb-1">
                    <div class="flex items-center text-xs ${textColor}">
                        <span class="mr-1">${alerte.icon}</span>
                        <span>${alerte.message}</span>
                    </div>
                </div>
            `;
        });
        alertsContainer.innerHTML = alertsHTML;
    } else {
        alertsContainer.innerHTML = `
            <div class="rounded border border-green-200 bg-green-50 p-2">
                <div class="flex items-center text-xs text-green-800">
                    <span class="mr-1">✅</span>
                    <span>Stock suffisant (${couleurTrouvee ? stockCouleur : stockTotal} disponible)</span>
                </div>
            </div>
        `;
    }
}

// Mettre à jour le total d'un produit
function updateProductTotal(index) {
    const row = document.querySelector(`tr[data-index="${index}"]`);
    const quantite = parseInt(row.querySelector('.quantite-input').value) || 0;
    const prix = parseFloat(row.querySelector('.prix-input').value) || 0;
    const total = quantite * prix;

    row.querySelector('.prix-total').textContent = total.toFixed(2);
    updateOrderTotal();


}

// Mettre à jour le total de la commande
function updateOrderTotal() {
    let subtotal = 0;
    document.querySelectorAll('.prix-total').forEach(element => {
        subtotal += parseFloat(element.textContent) || 0;
    });

    // Calculer les frais de livraison
    const villeSelect = document.querySelector('select[name="ville"]');
    let livraison = 0;
    if (villeSelect.value) {
        const cityConfig = @json(config('delivery.cities', []));
        if (cityConfig[villeSelect.value]) {
            livraison = cityConfig[villeSelect.value].price || 0;
        }
    }

    const total = subtotal + livraison;

    document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' MAD';
    document.getElementById('livraison').textContent = livraison.toFixed(2) + ' MAD';
    document.getElementById('totalCommande').textContent = total.toFixed(2) + ' MAD';
}

// Mettre à jour les frais de livraison quand la ville change
document.querySelector('select[name="ville"]').addEventListener('change', updateOrderTotal);

// Initialiser les totaux au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Mettre à jour tous les totaux des produits existants
    document.querySelectorAll('.product-row').forEach((row, index) => {
        // Ajouter des événements pour les changements de couleur et taille
        const couleurSelect = row.querySelector('.couleur-select');
        const tailleSelect = row.querySelector('.taille-select');
        const quantiteInput = row.querySelector('.quantite-input');


        updateProductTotal(index);
    });

    // Mettre à jour le total de la commande
    updateOrderTotal();
});
</script>
@endsection


