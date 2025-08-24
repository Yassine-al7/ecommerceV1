@extends('layouts.app')

@section('title', isset($order) ? 'Modifier Commande' : 'Créer Commande')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ isset($order) ? 'Modifier' : 'Créer' }} une commande</h1>
            <p class="text-gray-600 mt-2">Remplissez les informations pour {{ isset($order) ? 'modifier' : 'créer' }} une nouvelle commande</p>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ isset($order) ? route('seller.orders.update', $order->id) : route('seller.orders.store') }}" class="space-y-6" onsubmit="return validateFormBeforeSubmit()">
                @csrf
                @if(isset($order))
                    @method('PUT')
                @endif

                <!-- Informations client -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Informations client
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom client *</label>
                            <input type="text" name="nom_client" value="{{ old('nom_client', $order->nom_client ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                            <select name="ville" id="villeSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Sélectionnez une ville</option>
                                <option value="Casablanca" @selected(old('ville', $order->ville ?? '') == 'Casablanca')>Casablanca - 15 DH (1-2 jours)</option>
                                <option value="Rabat" @selected(old('ville', $order->ville ?? '') == 'Rabat')>Rabat - 20 DH (1-2 jours)</option>
                                <option value="Fès" @selected(old('ville', $order->ville ?? '') == 'Fès')>Fès - 25 DH (2-3 jours)</option>
                                <option value="Marrakech" @selected(old('ville', $order->ville ?? '') == 'Marrakech')>Marrakech - 25 DH (2-3 jours)</option>
                                <option value="Agadir" @selected(old('ville', $order->ville ?? '') == 'Agadir')>Agadir - 30 DH (2-3 jours)</option>
                                <option value="Tanger" @selected(old('ville', $order->ville ?? '') == 'Tanger')>Tanger - 30 DH (2-3 jours)</option>
                                <option value="Meknès" @selected(old('ville', $order->ville ?? '') == 'Meknès')>Meknès - 25 DH (2-3 jours)</option>
                                <option value="Oujda" @selected(old('ville', $order->ville ?? '') == 'Oujda')>Oujda - 35 DH (3-4 jours)</option>
                                <option value="Tétouan" @selected(old('ville', $order->ville ?? '') == 'Tétouan')>Tétouan - 30 DH (2-3 jours)</option>
                                <option value="El Jadida" @selected(old('ville', $order->ville ?? '') == 'El Jadida')>El Jadida - 20 DH (1-2 jours)</option>
                                <option value="Safi" @selected(old('ville', $order->ville ?? '') == 'Safi')>Safi - 25 DH (2-3 jours)</option>
                                <option value="Béni Mellal" @selected(old('ville', $order->ville ?? '') == 'Béni Mellal')>Béni Mellal - 25 DH (2-3 jours)</option>
                                <option value="Kénitra" @selected(old('ville', $order->ville ?? '') == 'Kénitra')>Kénitra - 20 DH (1-2 jours)</option>
                                <option value="Témara" @selected(old('ville', $order->ville ?? '') == 'Témara')>Témara - 18 DH (1-2 jours)</option>
                                <option value="Mohammedia" @selected(old('ville', $order->ville ?? '') == 'Mohammedia')>Mohammedia - 18 DH (1-2 jours)</option>
                                <option value="Autre" @selected(old('ville', $order->ville ?? '') == 'Autre')>Autre - 40 DH (3-5 jours)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Le prix de livraison sera calculé automatiquement <strong>par commande</strong></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse *</label>
                            <input type="text" name="adresse_client" value="{{ old('adresse_client', $order->adresse_client ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                            <input type="text" name="numero_telephone_client" value="{{ old('numero_telephone_client', $order->numero_telephone_client ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                </div>

                <!-- Informations produits -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center justify-between">
                        <span><i class="fas fa-box mr-2 text-green-600"></i>Produits de la commande</span>
                        <button type="button" onclick="refreshStockDisplay()" class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105" title="Rafraîchir l'affichage du stock">
                            <i class="fas fa-sync-alt mr-2"></i>Rafraîchir Stock
                        </button>
                    </h3>

                    <div id="productsContainer">
                        <!-- Premier produit -->
                        <div class="product-item border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50" data-product-index="0">
                                                    <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">Produit #1</h4>
                            <div class="flex items-center space-x-2">
                                <button type="button" class="add-product-btn text-green-600 hover:text-green-800" onclick="addProduct()" title="Ajouter un produit">
                                    <i class="fas fa-plus-circle text-lg"></i>
                                </button>
                                <button type="button" class="edit-product-btn text-blue-600 hover:text-blue-800" onclick="editProduct(this)" title="Modifier ce produit">
                                    <i class="fas fa-edit text-lg"></i>
                                </button>
                                <button type="button" class="remove-product-btn text-red-600 hover:text-red-800" onclick="removeProduct(this)" style="display: none;" title="Supprimer ce produit">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Produit *</label>
                                    <select name="products[0][product_id]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Sélectionnez un produit</option>
                                @foreach(($products ?? []) as $p)
                                            <option value="{{ $p->id }}"
                                                    data-image="{{ $p->image }}"
                                                    data-prix-admin="{{ optional($p->pivot)->prix_vente ?? $p->prix_admin }}"
                                                    data-tailles="{{ $p->tailles ? json_encode($p->tailles) : '[]' }}">
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Couleur *</label>
                            <select name="products[0][couleur_produit]" class="color-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Sélectionnez d'abord un produit</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Taille *</label>
                                    <select name="products[0][taille_produit]" class="size-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Sélectionnez d'abord un produit</option>
                            </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité *</label>
                                    <input type="number" name="products[0][quantite_produit]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="1" min="1" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix de vente au client (DH) *</label>
                                    <input type="number" name="products[0][prix_vente_client]" class="prix-vente-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" step="0.01" min="0.01" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix d'achat (DH)</label>
                                    <input type="text" class="prix-achat-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-semibold" readonly>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Marge par produit (DH)</label>
                                    <input type="text" class="marge-produit-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-blue-50 text-blue-700 font-semibold text-center" readonly>
                                </div>
                            </div>

                            <!-- Image du produit -->
                            <div class="product-image mt-4 hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Image du produit</label>
                                <div class="w-32 h-32 border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                    <img class="w-full h-full object-cover" alt="Image produit">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calculs totaux -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2 text-purple-600"></i>
                        Résumé de la commande
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prix total commande (DH)</label>
                            <input type="text" id="prixTotalCommande" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-blue-50 text-blue-700 font-semibold text-center text-lg" readonly>
                            <p class="text-xs text-gray-500 mt-1">💡 <strong>Note:</strong> Le prix total est la somme des prix de vente au client (pas × quantité)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prix de livraison (DH)</label>
                            <input type="text" id="prixLivraison" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-yellow-50 text-yellow-700 font-semibold text-center" readonly>
                            <p id="deliveryInfo" class="text-xs text-gray-500 mt-1">Sélectionnez une ville</p>
                            <p class="text-xs text-blue-600 mt-1 font-medium">💡 <strong>Note:</strong> Le prix de livraison est calculé <strong>par commande</strong>, pas par produit</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Marge bénéfice totale (DH)</label>
                            <input type="text" id="margeBeneficeTotale" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-green-50 text-green-700 font-semibold text-center text-lg" readonly>
                            <p class="text-xs text-gray-500 mt-1">Calculée automatiquement : Marge produits - Prix de livraison</p>
                        </div>
                    </div>
                </div>

                <!-- Commentaire -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-comment mr-2 text-purple-600"></i>
                        Commentaire
                    </h3>
                    <textarea name="commentaire" rows="3" placeholder="Ajoutez un commentaire optionnel..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('commentaire', $order->commentaire ?? '') }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('seller.orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>{{ isset($order) ? 'Mettre à jour' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
// Fallback config depuis Laravel si l'API n'est pas disponible
const sellerCitiesConfig = @json(config('delivery.cities', []));
let deliveryConfig = {
    default_price: 0,
    prices: {},
    zones: {},
    cities: {},
    rules: {}
};

let productCounter = 1;

// Données des produits passées depuis PHP
const productsData = @json($products ?? []);

// Récupérer la configuration des prix de livraison
async function loadDeliveryConfig() {
    try {
        const response = await fetch('/api/delivery-config');
        deliveryConfig = await response.json();
        console.log('Configuration de livraison chargée:', deliveryConfig);
    } catch (error) {
        console.error('Erreur lors du chargement de la configuration de livraison:', error);
    }
    // Initialiser l'affichage si une ville est déjà sélectionnée
    try { updateDeliveryPrice(); } catch (_) {}
}

// Normaliser les chaînes pour comparer (insensible à la casse/accents/espaces)
function normalizeCityKey(str) {
    return (str || '')
        .toString()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // enlever accents
        .replace(/\s+/g, '_');
}

// Trouver la config ville par clé ou par nom (API ou fallback Laravel)
function getCityConfigByValue(cityValue) {
    const apiCities = (deliveryConfig && deliveryConfig.cities) ? deliveryConfig.cities : {};
    const laravelCities = sellerCitiesConfig || {};

    // 1) Clé exacte API
    if (apiCities[cityValue]) return apiCities[cityValue];
    // 2) Clé normalisée API
    let match = Object.keys(apiCities).find(k => normalizeCityKey(k) === normalizeCityKey(cityValue));
    if (match) return apiCities[match];
    // 3) Nom égal API
    match = Object.keys(apiCities).find(k => normalizeCityKey(apiCities[k]?.name) === normalizeCityKey(cityValue));
    if (match) return apiCities[match];

    // 4) Clé exacte Laravel
    if (laravelCities[cityValue]) return laravelCities[cityValue];
    // 5) Clé normalisée Laravel
    match = Object.keys(laravelCities).find(k => normalizeCityKey(k) === normalizeCityKey(cityValue));
    if (match) return laravelCities[match];
    // 6) Nom égal Laravel
    match = Object.keys(laravelCities).find(k => normalizeCityKey(laravelCities[k]?.name) === normalizeCityKey(cityValue));
    if (match) return laravelCities[match];

    return null;
}

function updateDeliveryPrice() {
    const villeSelect = document.getElementById('villeSelect');
    const prixLivraison = document.getElementById('prixLivraison');
    const deliveryInfo = document.getElementById('deliveryInfo');

    const cityKey = villeSelect.value;
    const cityConfig = getCityConfigByValue(cityKey);

    // 1) Source de vérité prioritaire: le prix affiché dans l'option (évite les écarts 15 vs 30)
    let prixFromOption = null;
    const selectedOption = villeSelect.options[villeSelect.selectedIndex];
    if (selectedOption) {
        // Essayer d'abord data-price si présent
        const dp = selectedOption.getAttribute('data-price');
        if (dp) {
            const p = parseFloat((dp + '').replace(',', '.'));
            if (!isNaN(p)) prixFromOption = p;
        }
        // Sinon parser le texte: "Ville - 15 DH (.. )"
        if (prixFromOption === null) {
            const txt = selectedOption.textContent || '';
            const m = txt.match(/(\d+[\.,]?\d*)\s*DH/i);
            if (m) {
                const p = parseFloat(m[1].replace(',', '.'));
                if (!isNaN(p)) prixFromOption = p;
            }
        }
    }

    if (cityKey && (cityConfig || prixFromOption !== null)) {
        const prix = (prixFromOption !== null)
            ? prixFromOption
            : (cityConfig?.price ?? 0);
        const temps = cityConfig.delivery_time || (cityConfig.name ? cityConfig.name : '');
        const zone = cityConfig.zone || (cityConfig.type || '');

        prixLivraison.value = prix.toFixed(2);
        deliveryInfo.textContent = `${temps} - Zone: ${zone}`;

        // Changer la couleur selon la zone
        if (zone === 'local') {
            prixLivraison.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-green-50 text-green-700 font-semibold text-center';
        } else if (zone === 'regional') {
            prixLivraison.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-yellow-50 text-yellow-700 font-semibold text-center';
        } else {
            prixLivraison.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-red-50 text-red-700 font-semibold text-center';
        }
    } else {
        prixLivraison.value = '0.00';
        deliveryInfo.textContent = 'Sélectionnez une ville';
        prixLivraison.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-semibold text-center';
    }

    // Recalculer les totaux avec protection
    safeCalculateTotals();
}

function addProduct() {
    const container = document.getElementById('productsContainer');
    const newProduct = document.createElement('div');
    newProduct.className = 'product-item border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50';
    newProduct.setAttribute('data-product-index', productCounter);

    newProduct.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-medium text-gray-900">Produit #${productCounter + 1}</h4>
            <div class="flex items-center space-x-2">
                <button type="button" class="add-product-btn text-green-600 hover:text-green-800" onclick="addProduct()" title="Ajouter un produit">
                    <i class="fas fa-plus-circle text-lg"></i>
                </button>
                <button type="button" class="edit-product-btn text-blue-600 hover:text-blue-800" onclick="editProduct(this)" title="Modifier ce produit">
                    <i class="fas fa-edit text-lg"></i>
                </button>
                <button type="button" class="remove-product-btn text-red-600 hover:text-red-800" onclick="removeProduct(this)" title="Supprimer ce produit">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Produit *</label>
                <select name="products[${productCounter}][product_id]" class="product-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:ring-blue-500" required>
                    <option value="">Sélectionnez un produit</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Couleur *</label>
                <select name="products[${productCounter}][couleur_produit]" class="color-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Sélectionnez d'abord un produit</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Taille *</label>
                <select name="products[${productCounter}][taille_produit]" class="size-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Sélectionnez d'abord un produit</option>
                </select>
                <!-- Les notes d'information des tailles seront ajoutées ici dynamiquement -->
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantité *</label>
                <input type="number" name="products[${productCounter}][quantite_produit]" class="quantity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="1" min="1" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Prix de vente au client (DH) *</label>
                <input type="number" name="products[${productCounter}][prix_vente_client]" class="prix-vente-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" step="0.01" min="0.01" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Prix d'achat (DH)</label>
                <input type="text" class="prix-achat-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-semibold" readonly>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Marge par produit (DH)</label>
                <input type="text" class="marge-produit-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-blue-50 text-blue-700 font-semibold text-center" readonly>
            </div>
        </div>

        <!-- Image du produit -->
        <div class="product-image mt-4 hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Image du produit</label>
            <div class="w-32 h-32 border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                <img class="w-full h-full object-cover" alt="Image produit">
            </div>
        </div>
    `;

    container.appendChild(newProduct);
    productCounter++;

    // Remplir les options des produits
    const productSelect = newProduct.querySelector('.product-select');
    productsData.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = product.name;
        option.setAttribute('data-image', product.image || '');
        option.setAttribute('data-prix-admin', product.pivot?.prix_vente || product.prix_admin || '0');
        option.setAttribute('data-tailles', product.tailles ? JSON.stringify(product.tailles) : '[]');
        productSelect.appendChild(option);
    });

    // Ajouter les événements au nouveau produit
    console.log(`🆕 Configuration des événements pour le nouveau Produit #${productCounter}`);
    setupProductEvents(newProduct);

    // Calculer le prix d'achat initial pour le nouveau produit
    calculatePurchasePrice(newProduct);

    // Mettre à jour l'affichage du bouton de suppression
    updateRemoveButtons();
}

function editProduct(button) {
    const productItem = button.closest('.product-item');
    const productTitle = productItem.querySelector('h4')?.textContent || 'Produit';

    console.log(`✏️ Modification du ${productTitle}`);

    // Mettre en surbrillance le produit en cours de modification
    productItem.classList.add('ring-2', 'ring-blue-500', 'ring-opacity-50');

    // Afficher un message d'information
    const infoMessage = document.createElement('div');
    infoMessage.className = 'bg-blue-50 border border-blue-200 text-blue-800 px-4 py-2 rounded-lg mb-3 text-sm';
    infoMessage.innerHTML = `<i class="fas fa-info-circle mr-2"></i>Modification du ${productTitle} - Modifiez les champs ci-dessous puis sauvegardez`;

    // Insérer le message après le titre
    const titleContainer = productItem.querySelector('.flex.items-center.justify-between');
    titleContainer.parentNode.insertBefore(infoMessage, titleContainer.nextSibling);

    // Supprimer la surbrillance et le message après 3 secondes
    setTimeout(() => {
        productItem.classList.remove('ring-2', 'ring-blue-500', 'ring-opacity-50');
        if (infoMessage.parentNode) {
            infoMessage.remove();
        }
    }, 3000);

    // Faire défiler vers le produit
    productItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeProduct(button) {
    const productItem = button.closest('.product-item');
    productItem.remove();

    // Mettre à jour l'affichage du bouton de suppression
    updateRemoveButtons();

    // Recalculer les totaux avec protection
    safeCalculateTotals();
}

function updateRemoveButtons() {
    const productItems = document.querySelectorAll('.product-item');
    const removeButtons = document.querySelectorAll('.remove-product-btn');
    const addButtons = document.querySelectorAll('.add-product-btn');

    if (productItems.length === 1) {
        // Cacher le bouton de suppression pour le premier produit
        removeButtons.forEach(btn => btn.style.display = 'none');
        // Garder le bouton d'ajout visible
        addButtons.forEach(btn => btn.style.display = 'block');
    } else {
        // Afficher tous les boutons
        removeButtons.forEach(btn => btn.style.display = 'block');
        addButtons.forEach(btn => btn.style.display = 'block');
    }
}

function setupProductEvents(productItem) {
    const productSelect = productItem.querySelector('.product-select');
    const sizeSelect = productItem.querySelector('.size-select');
    const quantityInput = productItem.querySelector('.quantity-input');
    const prixVenteInput = productItem.querySelector('.prix-vente-input');
    const prixAchatDisplay = productItem.querySelector('.prix-achat-display');
    const margeProduitDisplay = productItem.querySelector('.marge-produit-display');
    const productImage = productItem.querySelector('.product-image');
    const productImageImg = productItem.querySelector('.product-image img');

    // Identifier le produit
    const productTitle = productItem.querySelector('h4')?.textContent || 'Produit inconnu';
    console.log(`🔧 Configuration des événements pour: ${productTitle}`);

                // Événement de sélection de produit
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const productName = selectedOption.textContent || 'Produit inconnu';
                console.log(`📦 Produit sélectionné dans ${productTitle}:`, selectedOption.value);
                console.log(`📦 Nom du produit: ${productName}`);

                if (selectedOption.value) {
            const image = selectedOption.getAttribute('data-image');
            const prixAdmin = selectedOption.getAttribute('data-prix-admin');
            const taillesRaw = selectedOption.getAttribute('data-tailles');

            console.log('📊 Données du produit:');
            console.log('  - Image:', image);
            console.log('  - Prix admin:', prixAdmin);
            console.log('  - Tailles raw:', taillesRaw);
            console.log('  - Produit:', productTitle);
            console.log('  - ID Produit:', selectedOption.value);

                                // Parser les tailles avec gestion d'erreur et conversion forcée
                    let tailles = [];
                    console.log(`🔍 DEBUGGING TAILLES pour ${productName}:`);
                    console.log('  - Contenu brut taillesRaw:', taillesRaw);
                    console.log('  - Type de taillesRaw:', typeof taillesRaw);

                    try {
                        if (taillesRaw && taillesRaw !== '[]' && taillesRaw !== 'null') {
                            // Essayer de parser d'abord
                            tailles = JSON.parse(taillesRaw);
                            console.log('  - Tailles parsées JSON:', tailles);
                            console.log('  - Type après parsing:', typeof tailles);
                            console.log('  - Est un tableau?:', Array.isArray(tailles));

                            // Si ce n'est pas un tableau mais que ça existe, le convertir
                            if (tailles && !Array.isArray(tailles)) {
                                console.log('⚠️ Tailles n\'est pas un tableau, conversion...');
                                if (typeof tailles === 'string') {
                                    // Si c'est une chaîne, la séparer par virgules
                                    tailles = tailles.split(',').map(t => t.trim().replace(/['"]/g, ''));
                                } else if (typeof tailles === 'object' && tailles.length !== undefined) {
                                    // Si c'est un objet avec length, le convertir en tableau
                                    tailles = Array.from(tailles);
                                } else {
                                    // Sinon, l'envelopper dans un tableau
                                    tailles = [tailles];
                                }
                                console.log('  - Tailles converties:', tailles);
                            }

                            // Nettoyer TOUS les caractères de formatage des tailles
                            if (Array.isArray(tailles)) {
                                tailles = tailles.map(taille => {
                                    if (typeof taille === 'string') {
                                        // Supprimer tous les caractères de formatage JSON : quotes, crochets, espaces
                                        return taille.replace(/[\[\]'"]/g, '').trim();
                                    }
                                    return taille;
                                });
                                console.log('  - Tailles nettoyées:', tailles);
                            }
                        } else {
                            console.log('⚠️ taillesRaw est vide, null ou []');
                            tailles = [];
                        }
                    } catch (error) {
                        console.error('❌ Erreur lors du parsing des tailles:', error);
                        console.log('  - Contenu brut des tailles:', taillesRaw);
                        tailles = [];
                    }

            // Afficher l'image
            if (image) {
                productImageImg.src = image;
                productImage.classList.remove('hidden');
                console.log('🖼️ Image affichée');
            } else {
                productImage.classList.add('hidden');
                console.log('❌ Pas d\'image disponible');
            }

            // Afficher le prix d'achat
            prixAchatDisplay.value = prixAdmin || '0.00';
            console.log('💰 Prix d\'achat affiché:', prixAchatDisplay.value);

            // Calculer le prix d'achat total selon la quantité actuelle
            calculatePurchasePrice(productItem);

                        // Remplir les couleurs disponibles
            const colorSelect = productItem.querySelector('.color-select');
            if (colorSelect) {
                colorSelect.innerHTML = '<option value="">Sélectionnez une couleur</option>';

                // Récupérer les couleurs du produit depuis la base de données
                const productId = selectedOption.value;
                const product = productsData.find(p => p.id == productId);

                if (product) {
                    console.log('🔍 Données complètes du produit:', product);
                    console.log('🔍 Champ quantite_stock:', product.quantite_stock);
                    console.log('🔍 Champ stock_couleurs:', product.stock_couleurs);
                    console.log('🔍 Champ couleur:', product.couleur);

                    let couleurs = [];

                                        // Essayer d'abord stock_couleurs
                    if (product.stock_couleurs) {
                        try {
                            const stockCouleurs = typeof product.stock_couleurs === 'string'
                                ? JSON.parse(product.stock_couleurs)
                                : product.stock_couleurs;

                            console.log('🔍 Debug stock_couleurs:', stockCouleurs);
                            console.log('🔍 Type stock_couleurs:', typeof stockCouleurs);
                            console.log('🔍 Est un tableau:', Array.isArray(stockCouleurs));
                            console.log('🔍 Contenu brut stock_couleurs:', JSON.stringify(product.stock_couleurs));
                            console.log('🔍 Contenu parsé stock_couleurs:', JSON.stringify(stockCouleurs));

                            if (Array.isArray(stockCouleurs)) {
                                // Récupérer le stock total du produit
                                const stockTotal = product.quantite_stock || 0;
                                console.log('🔍 Stock total du produit:', stockTotal);
                                console.log('🔍 Champ quantite_stock:', product.quantite_stock);

                                couleurs = stockCouleurs.map(sc => {
                                    let quantity = sc.quantity || 0;
                                    console.log(`🔍 Couleur ${sc.name}: stock original = ${sc.quantity}, stock total = ${stockTotal}`);

                                    // Utiliser le stock réel de la couleur (pas de fallback)
                                    console.log(`✅ Stock utilisé pour ${sc.name}: ${quantity} (stock réel)`);

                                    return {
                                        name: sc.name,
                                        quantity: quantity
                                    };
                                });
                                console.log(`✅ Couleurs trouvées dans stock_couleurs:`, couleurs);
                            }
                        } catch (error) {
                            console.error('❌ Erreur lors du parsing de stock_couleurs:', error);
                        }
                    }

                                        // Fallback sur le champ couleur SEULEMENT si stock_couleurs est vraiment vide
                    if (couleurs.length === 0 && product.couleur) {
                        try {
                            const couleurData = typeof product.couleur === 'string'
                                ? JSON.parse(product.couleur)
                                : product.couleur;

                            if (Array.isArray(couleurData)) {
                                // Utiliser le stock total du produit comme fallback
                                const stockTotal = product.quantite_stock || 10;
                                console.log(`🔍 Fallback: utilisation du stock total ${stockTotal} pour toutes les couleurs`);

                                couleurs = couleurData.map(c => ({
                                    name: typeof c === 'string' ? c : c.name || 'Couleur',
                                    quantity: stockTotal
                                }));
                                console.log(`✅ Couleurs trouvées dans couleur (fallback):`, couleurs);
                            }
                        } catch (error) {
                            console.error('❌ Erreur lors du parsing de couleur:', error);
                        }
                    }

                    // Debug final des couleurs qui seront affichées
                    console.log(`🔍 FINAL - Couleurs à afficher:`, couleurs);
                    console.log(`🔍 FINAL - Source des données:`, couleurs.length > 0 ? 'stock_couleurs' : 'fallback couleur');

                                        // Afficher toutes les couleurs trouvées avec leur vrai stock
                    if (couleurs.length > 0) {
                        let couleursDisponibles = 0;

                        couleurs.forEach(couleur => {
                            // Debug détaillé de chaque couleur
                            console.log(`🔍 Debug couleur "${couleur.name}":`, {
                                name: couleur.name,
                                quantity: couleur.quantity,
                                type: typeof couleur.quantity,
                                isZero: couleur.quantity <= 0,
                                isStrictlyZero: couleur.quantity === 0,
                                parsedQuantity: parseInt(couleur.quantity),
                                parsedIsZero: parseInt(couleur.quantity) <= 0
                            });

                            const option = document.createElement('option');
                            option.value = couleur.name;

                            // Vérifier si la quantité est vraiment 0 ou négative
                            const stockQuantity = parseInt(couleur.quantity) || 0;

                            if (stockQuantity <= 0) {
                                // Couleur en rupture de stock
                                option.textContent = `${couleur.name} (en stock : 0)`;
                                option.setAttribute('data-stock', stockQuantity);
                                option.disabled = true;
                                option.style.color = '#999';
                                option.style.fontStyle = 'italic';
                                console.log(`⚠️ Couleur en rupture affichée: ${couleur.name} (stock: ${stockQuantity})`);
                            } else {
                                // Couleur disponible
                                couleursDisponibles++;
                                option.textContent = `${couleur.name} (en stock : ${stockQuantity})`;
                                option.setAttribute('data-stock', stockQuantity);
                                console.log(`🎨 Couleur disponible ajoutée: ${couleur.name} (stock: ${stockQuantity})`);
                            }

                            colorSelect.appendChild(option);
                        });

                        console.log(`✅ Total couleurs: ${couleurs.length} (${couleursDisponibles} disponibles, ${couleurs.length - couleursDisponibles} en rupture)`);

                        // Si aucune couleur disponible, afficher un message d'avertissement
                        if (couleursDisponibles === 0) {
                            const warningOption = document.createElement('option');
                            warningOption.value = '';
                            warningOption.textContent = '⚠️ Toutes les couleurs sont en rupture de stock';
                            warningOption.disabled = true;
                            warningOption.style.color = '#ff6b6b';
                            warningOption.style.fontWeight = 'bold';
                            colorSelect.insertBefore(warningOption, colorSelect.firstChild);
                            console.log('⚠️ Toutes les couleurs sont en rupture de stock');
                        }
                    } else {
                        console.log('⚠️ Aucune couleur trouvée pour ce produit');
                        // Ajouter une option par défaut
                        const option = document.createElement('option');
                        option.value = 'Couleur unique';
                        option.textContent = 'Couleur unique (en stock : 10)';
                        option.setAttribute('data-stock', 10);
                        colorSelect.appendChild(option);
                        console.log('🎨 Couleur par défaut ajoutée: Couleur unique');
                    }
                }

                // Réinitialiser la sélection de couleur
                colorSelect.value = '';

                // Ajouter la validation de stock pour les couleurs
                colorSelect.addEventListener('change', function() {
                    validateStockQuantity(productItem);
                });
            }

                                            // Remplir les tailles
                    sizeSelect.innerHTML = '<option value="">Sélectionnez une taille</option>';

                    // Supprimer les anciennes notes d'information
                    const oldNotes = sizeSelect.parentElement.querySelectorAll('p.text-xs');
                    oldNotes.forEach(note => note.remove());

                    // Réinitialiser la sélection de taille
                    sizeSelect.value = '';

                        // Gestion des accessoires par catégorie (plus précise)
            const productId = selectedOption.value;
            const product = productsData.find(p => p.id == productId);

            // Debug des données de catégorie
            console.log('🔍 Debug catégorie pour produit ID:', productId);
            console.log('🔍 Produit trouvé:', product);
            console.log('🔍 Catégorie du produit:', product?.category);
            console.log('🔍 Slug de la catégorie:', product?.category?.slug);
            console.log('🔍 Nom de la catégorie:', product?.category?.name);
            console.log('🔍 Toutes les données du produit:', JSON.stringify(product, null, 2));

            // Détection des accessoires : d'abord par catégorie, puis par fallback sur les tailles
            let isAccessoire = false;

            if (product && product.category && product.category.slug === 'accessoires') {
                isAccessoire = true;
                console.log('✅ Accessoire détecté par catégorie');
            } else if (!tailles || tailles.length === 0) {
                isAccessoire = true;
                console.log('✅ Accessoire détecté par fallback (pas de tailles)');
            } else {
                console.log('📏 Produit avec tailles détecté');
            }

            console.log('🔍 Est un accessoire?', isAccessoire);

            if (isAccessoire) {
                // Pour les accessoires, masquer le champ taille et le rendre non obligatoire
                const tailleContainer = sizeSelect.parentElement;
                tailleContainer.style.display = 'none';
                sizeSelect.removeAttribute('required');
                sizeSelect.value = 'N/A';
                console.log('✅ Accessoire détecté par catégorie - Champ taille masqué et non obligatoire');
            } else {
                // Pour les produits avec tailles, afficher le champ et le rendre obligatoire
                const tailleContainer = sizeSelect.parentElement;
                tailleContainer.style.display = 'block';
                sizeSelect.setAttribute('required', 'required');
                console.log('📏 Produit avec tailles - Champ taille affiché et obligatoire');
            }



            console.log('🔍 Vérification des tailles:');
            console.log('  - Type:', typeof tailles);
            console.log('  - Est un tableau:', Array.isArray(tailles));
            console.log('  - Longueur:', tailles?.length);
            console.log('  - Contenu:', tailles);

                        // Vérifier que tailles est bien un tableau
            console.log('🔍 Détails des tailles:');
            console.log('  - tailles:', tailles);
            console.log('  - typeof:', typeof tailles);
            console.log('  - Array.isArray:', Array.isArray(tailles));
            console.log('  - length:', tailles?.length);

            if (tailles && Array.isArray(tailles) && tailles.length > 0) {
                console.log('✅ Tailles valides détectées');
                                    tailles.forEach(taille => {
                        const option = document.createElement('option');
                        // Triple nettoyage pour être sûr
                        const tailleClean = taille.replace(/[\[\]'"]/g, '').trim();
                        option.value = tailleClean;
                        option.textContent = tailleClean;
                        sizeSelect.appendChild(option);
                        console.log(`  📏 Taille ajoutée: "${tailleClean}" (original: "${taille}")`);
                    });
                console.log(`📏 Tailles disponibles: ${tailles.join(', ')}`);
                console.log(`📏 Tailles affichées: ${tailles.join(' | ')}`);
            } else {
                // Si pas de tailles définies ou format invalide, ajouter "Taille unique"
                console.log('❌ Tailles invalides ou manquantes');
                console.log('   Raisons possibles:');
                console.log(`   - tailles existe: ${!!tailles}`);
                console.log(`   - est un tableau: ${Array.isArray(tailles)}`);
                console.log(`   - a une longueur > 0: ${tailles?.length > 0}`);

                // Si des tailles existent malgré tout, les utiliser
                if (tailles && tailles.length > 0) {
                    console.log('✅ Tailles du produit trouvées dans la base de données');
                    tailles.forEach(taille => {
                        const option = document.createElement('option');
                        // Triple nettoyage pour être sûr
                        const tailleClean = taille.replace(/[\[\]'"]/g, '').trim();
                        option.value = tailleClean;
                        option.textContent = tailleClean;
                        sizeSelect.appendChild(option);
                        console.log(`  📏 Taille ajoutée: "${tailleClean}" (original: "${taille}")`);
                    });
                    console.log(`📏 Tailles du produit: ${tailles.join(', ')}`);

                    // Ajouter une note informatif
                    const noteInfo = document.createElement('p');
                    noteInfo.className = 'text-xs text-green-600 mt-1';
                    noteInfo.innerHTML = '✅ <strong>Tailles définies par l\'admin</strong>';
                    sizeSelect.parentElement.appendChild(noteInfo);

                } else {
                    // Si aucune taille n'est trouvée, c'est un accessoire
                    console.log('✅ Accessoire confirmé - Pas de tailles ajoutées');
                    console.log('📏 Aucune taille disponible pour cet accessoire');

                    // Pour les accessoires, on n'ajoute pas d'options car le champ est masqué
                    // La valeur 'N/A' est déjà définie dans la logique de catégorie
                }
            }



            // Recalculer la marge
            calculateProductMargin(productItem);
        } else {
            productImage.classList.add('hidden');
            prixAchatDisplay.value = '';
            sizeSelect.innerHTML = '<option value="">Sélectionnez d\'abord un produit</option>';
            margeProduitDisplay.value = '';
            console.log('❌ Aucun produit sélectionné');
        }

        safeCalculateTotals();
    });

    // Événement de sélection de taille
    sizeSelect.addEventListener('change', function() {
        if (this.value) {
            console.log(`📏 Taille sélectionnée: ${this.value}`);
            // Recalculer les totaux quand la taille change
            safeCalculateTotals();
        }
    });

    // Événements pour recalculer la marge
    quantityInput.addEventListener('input', () => {
        console.log('🔢 Quantité modifiée');
        // Calculer d'abord le nouveau prix d'achat selon la quantité
        calculatePurchasePrice(productItem);
        // Puis recalculer la marge
        calculateProductMargin(productItem);
        // Valider la quantité par rapport au stock
        validateStockQuantity(productItem);
        safeCalculateTotals();
    });

    prixVenteInput.addEventListener('input', () => {
        console.log('💵 Prix de vente modifié');
        calculateProductMargin(productItem);
        safeCalculateTotals();
    });

    // Initialiser si un produit est déjà sélectionné
    if (productSelect.value) {
        console.log('🔄 Initialisation avec produit déjà sélectionné');
        productSelect.dispatchEvent(new Event('change'));
    }
}

// Fonction de validation du stock
function validateStockQuantity(productItem) {
    const colorSelect = productItem.querySelector('.color-select');
    const quantityInput = productItem.querySelector('.quantity-input');

    if (!colorSelect || !quantityInput) return;

    const selectedColor = colorSelect.value;
    const selectedQuantity = parseInt(quantityInput.value) || 0;

    if (!selectedColor) {
        // Pas de couleur sélectionnée, pas de validation
        clearStockValidation(productItem);
        return;
    }

    // Récupérer le stock disponible pour cette couleur
    const selectedOption = colorSelect.options[colorSelect.selectedIndex];
    const availableStock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

    console.log(`🔍 Validation stock: Couleur=${selectedColor}, Quantité=${selectedQuantity}, Stock=${availableStock}`);

    if (selectedQuantity > availableStock) {
        // Quantité trop élevée
        showStockError(productItem, `Quantité (${selectedQuantity}) dépasse le stock disponible (${availableStock}) pour la couleur ${selectedColor}`);
        quantityInput.setCustomValidity(`Stock insuffisant. Maximum: ${availableStock}`);
        quantityInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    } else if (selectedQuantity <= 0) {
        // Quantité invalide
        showStockError(productItem, 'La quantité doit être supérieure à 0');
        quantityInput.setCustomValidity('La quantité doit être supérieure à 0');
        quantityInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
    } else {
        // Quantité valide
        clearStockValidation(productItem);
        quantityInput.setCustomValidity('');
        quantityInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        quantityInput.classList.add('border-green-500', 'focus:border-green-500', 'focus:ring-green-500');
    }
}

// Fonction pour afficher l'erreur de stock
function showStockError(productItem, message) {
    // Supprimer les anciens messages d'erreur
    clearStockValidation(productItem);

    // Créer le message d'erreur
    const errorDiv = document.createElement('div');
    errorDiv.className = 'stock-error text-sm text-red-600 mt-1 bg-red-50 border border-red-200 rounded px-2 py-1';
    errorDiv.innerHTML = `⚠️ <strong>Erreur de stock:</strong> ${message}`;

    // Insérer après le champ quantité
    const quantityContainer = productItem.querySelector('.quantity-input').parentElement;
    quantityContainer.appendChild(errorDiv);

    console.log(`❌ Erreur de stock affichée: ${message}`);
}

// Fonction pour effacer la validation de stock
function clearStockValidation(productItem) {
    const existingErrors = productItem.querySelectorAll('.stock-error');
    existingErrors.forEach(error => error.remove());

    const quantityInput = productItem.querySelector('.quantity-input');
    if (quantityInput) {
        quantityInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500', 'border-green-500', 'focus:border-green-500', 'focus:ring-green-500');
    }
}

function calculateProductMargin(productItem) {
    // Essayer plusieurs méthodes pour trouver l'élément marge
    let prixAchatDisplay = productItem.querySelector('.prix-achat-display');
    let prixVenteInput = productItem.querySelector('.prix-vente-input');
    let quantityInput = productItem.querySelector('.quantity-input');
    let margeProduitDisplay = productItem.querySelector('.marge-produit-display');

    // Si l'élément marge n'est pas trouvé, essayer d'autres méthodes
    if (!margeProduitDisplay) {
        console.log('🔍 Élément marge non trouvé, tentatives alternatives...');

        // Méthode 1: Chercher par label
        const labels = productItem.querySelectorAll('label');
        for (let label of labels) {
            if (label.textContent.includes('Marge')) {
                margeProduitDisplay = label.parentElement?.querySelector('input[readonly]');
                if (margeProduitDisplay) {
                    console.log('✅ Élément marge trouvé par label');
                    break;
                }
            }
        }

        // Méthode 2: Chercher tous les inputs readonly
        if (!margeProduitDisplay) {
            const readonlyInputs = productItem.querySelectorAll('input[readonly]');
            console.log(`Found ${readonlyInputs.length} readonly inputs`);
            readonlyInputs.forEach((input, i) => {
                console.log(`Input ${i}: classes="${input.className}"`);
                if (input.className.includes('blue') || input.previousElementSibling?.textContent?.includes('Marge')) {
                    margeProduitDisplay = input;
                    console.log('✅ Élément marge trouvé par inspection');
                }
            });
        }

        // Méthode 3: Créer l'élément s'il n'existe pas
        if (!margeProduitDisplay) {
            console.log('⚠️ Création forcée de l\'élément marge');
            const container = productItem.querySelector('.grid');
            if (container) {
                const div = document.createElement('div');
                div.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marge par produit (DH)</label>
                    <input type="text" class="marge-produit-display w-full px-3 py-2 border border-gray-300 rounded-lg bg-blue-50 text-blue-700 font-semibold text-center" readonly>
                `;
                container.appendChild(div);
                margeProduitDisplay = div.querySelector('.marge-produit-display');
                console.log('✅ Élément marge créé');
            }
        }
    }

    if (!prixAchatDisplay || !prixVenteInput || !quantityInput || !margeProduitDisplay) {
        console.error('❌ Éléments toujours manquants après tentatives');
        console.log('Éléments trouvés:', {
            prixAchatDisplay: !!prixAchatDisplay,
            prixVenteInput: !!prixVenteInput,
            quantityInput: !!quantityInput,
            margeProduitDisplay: !!margeProduitDisplay
        });
        return;
    }

    const prixAchatTotal = parseFloat(prixAchatDisplay.value) || 0;
    const prixVente = parseFloat(prixVenteInput.value) || 0;
    const quantite = parseInt(quantityInput.value) || 1;

    console.log(`\n=== Calcul de la marge pour ce produit ===`);
    console.log(`Prix d'achat total (prix admin × quantité): ${prixAchatTotal} DH`);
    console.log(`Prix de vente au client: ${prixVente} DH`);
    console.log(`Quantité: ${quantite}`);

    // Vérifier que les valeurs sont valides
    if (prixAchatTotal <= 0) {
        console.log('⚠️ Prix d\'achat total invalide ou manquant');
        margeProduitDisplay.value = '0.00';
        margeProduitDisplay.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-semibold text-center';
        return;
    }

    if (prixVente <= 0) {
        console.log('⚠️ Prix de vente invalide ou manquant');
        margeProduitDisplay.value = '0.00';
        margeProduitDisplay.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-semibold text-center';
        return;
    }

    if (quantite <= 0) {
        console.log('⚠️ Quantité invalide');
        margeProduitDisplay.value = '0.00';
        margeProduitDisplay.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-semibold text-center';
        return;
    }

    // NOUVELLE LOGIQUE : Marge = Prix de vente au client - Prix d'achat total
    // Où Prix d'achat total = Prix admin × Quantité
    const margeProduit = prixVente - prixAchatTotal;

    // Afficher la marge pour ce produit
    margeProduitDisplay.value = margeProduit.toFixed(2);

    // Changer la couleur selon la marge
    if (margeProduit > 0) {
        margeProduitDisplay.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-green-50 text-green-700 font-semibold text-center';
        console.log('✅ Marge positive (vert)');
    } else if (margeProduit < 0) {
        margeProduitDisplay.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-red-50 text-red-700 font-semibold text-center';
        console.log('❌ Marge négative (rouge)');
    } else {
        margeProduitDisplay.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-semibold text-center';
        console.log('⚪ Marge nulle (gris)');
    }

    console.log(`Nouvelle logique de calcul:`);
    console.log(`  Prix de vente au client: ${prixVente} DH`);
    console.log(`  Prix d'achat total (prix admin × quantité): ${prixAchatTotal} DH`);
    console.log(`  Marge produit: ${prixVente} - ${prixAchatTotal} = ${margeProduit.toFixed(2)} DH`);
    console.log(`Marge affichée: ${margeProduitDisplay.value} DH`);
}

// Nouvelle fonction pour calculer le prix d'achat selon la quantité
function calculatePurchasePrice(productItem) {
    const productSelect = productItem.querySelector('.product-select');
    const quantityInput = productItem.querySelector('.quantity-input');
    const prixAchatDisplay = productItem.querySelector('.prix-achat-display');

    if (!productSelect || !quantityInput || !prixAchatDisplay) {
        console.error('❌ Éléments manquants pour le calcul du prix d\'achat');
        return;
    }

    const selectedOption = productSelect.options[productSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        console.log('⚠️ Aucun produit sélectionné');
        prixAchatDisplay.value = '0.00';
        return;
    }

    const prixAdmin = parseFloat(selectedOption.getAttribute('data-prix-admin')) || 0;
    const quantite = parseInt(quantityInput.value) || 1;

    console.log(`\n=== Calcul du prix d'achat ===`);
    console.log(`Prix admin (prix unitaire): ${prixAdmin} DH`);
    console.log(`Quantité: ${quantite}`);
    console.log(`Prix d'achat total: ${prixAdmin} × ${quantite} = ${(prixAdmin * quantite).toFixed(2)} DH`);

    // Calculer le prix d'achat total selon la quantité
    const prixAchatTotal = prixAdmin * quantite;
    prixAchatDisplay.value = prixAchatTotal.toFixed(2);

    // Mettre à jour le label pour clarifier que c'est le prix total
    const label = prixAchatDisplay.previousElementSibling;
    if (label && label.tagName === 'LABEL') {
        label.textContent = `Prix d'achat total (${prixAdmin} DH × ${quantite})`;
    }

    console.log(`✅ Prix d'achat mis à jour: ${prixAchatDisplay.value} DH`);
}

function calculateTotals() {
    const productItems = document.querySelectorAll('.product-item');
    let prixTotalCommande = 0;
    let margeTotaleProduits = 0;

    console.log('=== Calcul des totaux de la commande ===');
    console.log(`Nombre de produits trouvés: ${productItems.length}`);

    productItems.forEach((item, index) => {
        // Vérifier que tous les éléments nécessaires existent
        const prixVenteInput = item.querySelector('.prix-vente-input');
        const quantityInput = item.querySelector('.quantity-input');
        let margeProduitDisplay = item.querySelector('.marge-produit-display');

        // Si l'élément marge n'est pas trouvé, essayer de le trouver par d'autres méthodes
        if (!margeProduitDisplay) {
            const readonlyInputs = item.querySelectorAll('input[readonly]');
            for (let input of readonlyInputs) {
                if (input.className.includes('blue') || input.previousElementSibling?.textContent?.includes('Marge')) {
                    margeProduitDisplay = input;
                    break;
                }
            }
        }

        if (!prixVenteInput || !quantityInput || !margeProduitDisplay) {
            console.error(`❌ Éléments manquants pour le produit #${index + 1}`);
            console.log(`   Prix vente: ${!!prixVenteInput}, Quantité: ${!!quantityInput}, Marge: ${!!margeProduitDisplay}`);
            return;
        }

        const prixVente = parseFloat(prixVenteInput.value) || 0;
        const quantite = parseInt(quantityInput.value) || 1;
        const margeProduit = parseFloat(margeProduitDisplay.value) || 0;

        console.log(`\nProduit #${index + 1}:`);
        console.log(`  Prix de vente: ${prixVente} DH`);
        console.log(`  Quantité: ${quantite}`);
        console.log(`  Marge produit: ${margeProduit} DH`);

        // 🚨 LOGIQUE MÉTIER CRITIQUE : Le prix total de la commande est le prix de vente au client
        // ❌ PAS le prix × quantité, mais juste le prix de vente fixe
        // ✅ C'est la logique métier demandée par l'utilisateur
        const prixProduit = prixVente; // Prix fixe, pas multiplié par la quantité
        prixTotalCommande += prixProduit;
        margeTotaleProduits += margeProduit;

        console.log(`  🎯 Prix total produit: ${prixProduit.toFixed(2)} DH (prix de vente fixe)`);
        console.log(`  📊 Marge totale produits (accumulé): ${margeTotaleProduits.toFixed(2)} DH`);
        console.log(`  💡 IMPORTANT: Quantité ${quantite} n'affecte PAS le prix total de la commande`);
    });

    const prixLivraison = parseFloat(document.getElementById('prixLivraison')?.value) || 0;

    // ✅ IMPORTANT: La livraison est calculée PAR COMMANDE, pas par produit
    // Une seule déduction du prix de livraison pour toute la commande
    console.log(`📦 Calcul de livraison: ${prixLivraison} DH pour TOUTE la commande (${productItems.length} produits)`);

    // Calcul selon la logique exacte de l'utilisateur
    // Marge finale = Marge totale pièces - Prix de livraison (UNE SEULE FOIS)
    const margeBeneficeTotale = margeTotaleProduits - prixLivraison;

    console.log(`\n=== Résumé des totaux ===`);
    console.log(`🎯 Prix total commande: ${prixTotalCommande.toFixed(2)} DH (SANS multiplication par quantité)`);
    console.log(`💰 Marge totale produits: ${margeTotaleProduits.toFixed(2)} DH`);
    console.log(`📦 Prix de livraison: ${prixLivraison.toFixed(2)} DH`);
    console.log(`💵 Marge bénéfice finale: ${margeTotaleProduits.toFixed(2)} - ${prixLivraison.toFixed(2)} = ${margeBeneficeTotale.toFixed(2)} DH`);

    // Mettre à jour les affichages
    const prixTotalElement = document.getElementById('prixTotalCommande');
    const margeTotaleElement = document.getElementById('margeBeneficeTotale');

    // Affichage du prix total commande APRÈS déduction de la livraison
    const prixTotalApresLivraison = prixTotalCommande - prixLivraison;

    if (prixTotalElement) {
        prixTotalElement.value = prixTotalApresLivraison.toFixed(2);
        console.log(`✅ Prix total commande (après livraison) mis à jour: ${prixTotalElement.value} DH`);

        // Vérification finale pour s'assurer que la logique est respectée
        if (prixTotalCommande > 0) {
            console.log(`🎯 CONFIRMATION: Prix total commande (avant livraison) = ${prixTotalCommande.toFixed(2)} DH`);
            console.log(`🎯 CONFIRMATION: Prix livraison = ${prixLivraison.toFixed(2)} DH`);
            console.log(`🎯 CONFIRMATION: Prix total (après livraison) = ${prixTotalApresLivraison.toFixed(2)} DH`);
        }
    } else {
        console.error('❌ Élément prixTotalCommande non trouvé');
    }

    if (margeTotaleElement) {
        margeTotaleElement.value = margeBeneficeTotale.toFixed(2);
        console.log(`✅ Marge bénéfice totale mise à jour: ${margeTotaleElement.value} DH`);

        // Changer la couleur de la marge totale
        if (margeBeneficeTotale > 0) {
            margeTotaleElement.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-green-50 text-green-700 font-semibold text-center text-lg';
        } else if (margeBeneficeTotale < 0) {
            margeTotaleElement.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-red-50 text-red-700 font-semibold text-center text-lg';
        } else {
            margeTotaleElement.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-semibold text-center text-lg';
        }
    } else {
        console.error('❌ Élément margeBeneficeTotale non trouvé');
    }
}

// Fonction de protection pour s'assurer que le prix total de la commande respecte la logique métier
function protectPrixTotalCommande() {
    const prixTotalElement = document.getElementById('prixTotalCommande');
    if (!prixTotalElement) return;

    // Vérifier que le prix total n'a pas été modifié incorrectement
    const currentValue = parseFloat(prixTotalElement.value) || 0;

    // Recalculer le prix total correct selon la logique métier
    const productItems = document.querySelectorAll('.product-item');
    let correctPrixTotal = 0;

    productItems.forEach((item) => {
        const prixVenteInput = item.querySelector('.prix-vente-input');
        if (prixVenteInput) {
            const prixVente = parseFloat(prixVenteInput.value) || 0;
            // 🎯 LOGIQUE MÉTIER : Prix fixe, pas × quantité
            correctPrixTotal += prixVente;
        }
    });

    // Si le prix total a été modifié incorrectement, le corriger
    if (Math.abs(currentValue - correctPrixTotal) > 0.01) {
        console.log(`🚨 CORRECTION: Prix total incorrect détecté`);
        console.log(`   Valeur actuelle: ${currentValue.toFixed(2)} DH`);
        console.log(`   Valeur correcte: ${correctPrixTotal.toFixed(2)} DH`);
        console.log(`   Correction appliquée`);

        prixTotalElement.value = correctPrixTotal.toFixed(2);
    }
}

// Appeler la protection après chaque mise à jour
function safeCalculateTotals() {
    calculateTotals();
    // Protection supplémentaire après le calcul
    setTimeout(protectPrixTotalCommande, 100);
}

function validateForm() {
    const productItems = document.querySelectorAll('.product-item');
    let isValid = true;

    console.log('=== Validation du formulaire ===');

    productItems.forEach((item, index) => {
        const productId = item.querySelector('.product-select').value;
        const taille = item.querySelector('.size-select').value;
        const quantite = item.querySelector('.quantity-input').value;
        const prixVente = item.querySelector('.prix-vente-input').value;
        const prixAchat = parseFloat(item.querySelector('.prix-achat-display').value) || 0;

        console.log(`Produit #${index + 1}:`);
        console.log(`  - Produit sélectionné: ${productId ? 'Oui' : 'Non'}`);
        console.log(`  - Taille sélectionnée: ${taille ? 'Oui' : 'Non'}`);
        console.log(`  - Quantité: ${quantite}`);
        console.log(`  - Prix de vente: ${prixVente}`);

        // Vérifier que tous les champs sont remplis
        if (!productId || !quantite || !prixVente) {
            isValid = false;
            console.log(`  ❌ Produit #${index + 1}: Champs manquants (produit, quantité, prix)`);
            return;
        }

        // Vérifier la taille seulement si le champ est visible (pas pour les accessoires)
        const sizeSelect = item.querySelector('.size-select');
        if (sizeSelect && sizeSelect.parentElement.style.display !== 'none') {
            if (!taille || taille.trim() === '') {
                isValid = false;
                console.log(`  ❌ Produit #${index + 1}: Taille manquante (champ visible)`);
                return;
            }
        } else {
            console.log(`  ✅ Produit #${index + 1}: Taille non requise (accessoire)`);
        }

        // Vérifier que le prix de vente est supérieur au prix d'achat
        if (parseFloat(prixVente) <= prixAchat) {
            isValid = false;
            console.log(`  ❌ Produit #${index + 1}: Prix de vente (${prixVente} DH) <= Prix d'achat (${prixAchat} DH)`);
            alert(`Produit #${index + 1}: Le prix de vente doit être supérieur au prix d'achat pour avoir une marge bénéfice.`);
            return;
        }

        // La validation de la taille est déjà faite plus haut avec la logique des accessoires

        console.log(`  ✅ Produit #${index + 1}: Valide`);
    });

    if (isValid) {
        console.log('✅ Tous les produits sont valides');
    } else {
        console.log('❌ Des erreurs ont été détectées');
    }

    return isValid;
}

function validateFormBeforeSubmit() {
    // Valider tous les produits avant l'envoi
    const productItems = document.querySelectorAll('.product-item');
    let isValid = true;

    productItems.forEach((productItem, index) => {
        const colorSelect = productItem.querySelector('.color-select');
        const quantityInput = productItem.querySelector('.quantity-input');
        const sizeSelect = productItem.querySelector('.size-select');

        if (colorSelect && quantityInput) {
            const selectedColor = colorSelect.value;
            const selectedQuantity = parseInt(quantityInput.value) || 0;

            if (!selectedColor) {
                showStockError(productItem, 'Veuillez sélectionner une couleur');
                isValid = false;
            } else if (selectedQuantity <= 0) {
                showStockError(productItem, 'La quantité doit être supérieure à 0');
                isValid = false;
            } else {
                // Vérifier le stock
                const selectedOption = colorSelect.options[colorSelect.selectedIndex];
                const availableStock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

                if (selectedQuantity > availableStock) {
                    showStockError(productItem, `Quantité (${selectedQuantity}) dépasse le stock disponible (${availableStock}) pour la couleur ${selectedColor}`);
                    isValid = false;
                }
            }

            // Validation de la taille (seulement si le champ est visible)
            if (sizeSelect && sizeSelect.parentElement.style.display !== 'none') {
                if (!sizeSelect.value || sizeSelect.value === '') {
                    showStockError(productItem, 'Veuillez sélectionner une taille');
                    isValid = false;
                }
            }
        }
    });

    if (!isValid) {
        alert('❌ Veuillez corriger les erreurs avant d\'envoyer la commande');
        return false;
    }

    // Si tout est valide, demander confirmation
    return confirmOrder();
}

function confirmOrder() {
    // Valider le formulaire avant de soumettre
    if (!validateForm()) {
        return false;
    }

    // Vérifier que tous les champs requis sont remplis
    const requiredFields = ['nom_client', 'ville', 'adresse_client', 'numero_telephone_client'];

    for (const fieldName of requiredFields) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field || !field.value.trim()) {
            alert(`Veuillez remplir le champ ${fieldName.replace('_', ' ')}.`);
            field?.focus();
            return false;
        }
    }

    // Vérifier qu'au moins un produit est sélectionné
    console.log('🔍 === Validation dans confirmOrder() ===');
    const productItems = document.querySelectorAll('.product-item');
    console.log(`🔍 Nombre de produits trouvés: ${productItems.length}`);
    let hasValidProduct = false;

    productItems.forEach((item, index) => {
        const productId = item.querySelector('.product-select').value;
        const taille = item.querySelector('.size-select').value;
        const quantite = item.querySelector('.quantity-input').value;
        const prixVente = item.querySelector('.prix-vente-input').value;
        const sizeSelect = item.querySelector('.size-select');

        console.log(`\n🔍 Analyse Produit #${index + 1}:`);
        console.log(`  - productId: "${productId}"`);
        console.log(`  - taille: "${taille}"`);
        console.log(`  - quantite: "${quantite}"`);
        console.log(`  - prixVente: "${prixVente}"`);
        console.log(`  - sizeSelect trouvé: ${!!sizeSelect}`);
        console.log(`  - sizeSelect parent: ${sizeSelect?.parentElement?.style?.display}`);

        // Vérifier si c'est un accessoire (champ taille masqué)
        const isAccessoire = sizeSelect && sizeSelect.parentElement.style.display === 'none';
        console.log(`  - Est accessoire: ${isAccessoire}`);

        // Validation adaptée selon le type de produit
        let isValid = false;
        if (isAccessoire) {
            // Pour les accessoires : taille non requise
            isValid = productId && quantite && prixVente;
            console.log(`  🔍 Accessoire - Taille non requise, validation: ${isValid}`);
        } else {
            // Pour les produits avec tailles : tous les champs requis
            isValid = productId && taille && quantite && prixVente;
            console.log(`  🔍 Produit avec tailles - Tous les champs requis, validation: ${isValid}`);
        }

        if (isValid) {
            hasValidProduct = true;
            console.log(`  ✅ Produit #${index + 1}: Valide`);
        } else {
            console.log(`  ❌ Produit #${index + 1}: Invalide`);
        }
    });

    console.log(`🔍 Résultat final: hasValidProduct = ${hasValidProduct}`);

    if (!hasValidProduct) {
        alert('Veuillez sélectionner au moins un produit avec toutes ses informations.');
        return false;
    }

    // Confirmation finale
    if (confirm('Êtes-vous sûr de vouloir créer cette commande ?')) {
        console.log('✅ Commande confirmée, soumission du formulaire...');

        // Soumettre le formulaire
        const form = document.querySelector('form');
        if (form) {
            // Ajouter un événement pour rafraîchir le stock après la soumission
            form.addEventListener('submit', function() {
                console.log('🔄 Formulaire soumis, rafraîchissement du stock en cours...');

                // Attendre un peu que la commande soit traitée, puis rafraîchir l'affichage
                setTimeout(() => {
                    refreshStockDisplay();
                }, 2000); // 2 secondes de délai
            });

            return true;
        }
    }

    return false;
}

// Fonction de debug spécifique pour l'élément marge
function debugMargeElement() {
    console.log('🔍 Debug spécifique pour l\'élément marge');

    const productItems = document.querySelectorAll('.product-item');
    productItems.forEach((item, index) => {
        console.log(`\n--- Produit #${index + 1} ---`);

        // Chercher tous les inputs de type text
        const textInputs = item.querySelectorAll('input[type="text"]');
        console.log(`Inputs de type text trouvés: ${textInputs.length}`);

        textInputs.forEach((input, i) => {
            console.log(`  Input #${i + 1}:`);
            console.log(`    Classes: "${input.className}"`);
            console.log(`    Readonly: ${input.readOnly}`);
            console.log(`    Value: "${input.value}"`);
            console.log(`    Label précédent:`, input.parentElement?.querySelector('label')?.textContent);

            // Vérifier si c'est l'élément marge
            if (input.className.includes('marge') || input.parentElement?.querySelector('label')?.textContent?.includes('Marge')) {
                console.log(`    🎯 TROUVÉ: Ceci semble être l'élément marge !`);
            }
        });
    });
}

// Fonction de debug pour vérifier les éléments
function debugElements() {
    console.log('=== Debug des éléments du DOM ===');

    const elements = [
        'prixTotalCommande',
        'margeBeneficeTotale',
        'prixLivraison',
        'villeSelect'
    ];

    elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            console.log(`✅ ${id}: trouvé, valeur = "${element.value}"`);
        } else {
            console.error(`❌ ${id}: NON TROUVÉ`);
        }
    });

    const productItems = document.querySelectorAll('.product-item');
    console.log(`\nProduits trouvés: ${productItems.length}`);

    productItems.forEach((item, index) => {
        console.log(`\n🔍 Analyse détaillée du Produit #${index + 1}:`);

        // Vérifier toutes les classes CSS recherchées
        const selectors = [
            '.product-select',
            '.size-select',
            '.quantity-input',
            '.prix-vente-input',
            '.prix-achat-display',
            '.marge-produit-display'
        ];

        selectors.forEach(selector => {
            const element = item.querySelector(selector);
            if (element) {
                console.log(`  ✅ ${selector}: trouvé`);
                if (element.value !== undefined) {
                    console.log(`     Valeur: "${element.value}"`);
                }
                if (element.className) {
                    console.log(`     Classes: "${element.className}"`);
                }
            } else {
                console.error(`  ❌ ${selector}: NON TROUVÉ`);
            }
        });

        // Vérifier tous les inputs dans ce produit
        const allInputs = item.querySelectorAll('input, select');
        console.log(`  📝 Total inputs/selects trouvés: ${allInputs.length}`);
        allInputs.forEach((input, i) => {
            console.log(`     Input #${i + 1}: type="${input.type || input.tagName}", class="${input.className}", value="${input.value}"`);
        });
    });
}

// Fonction pour pré-remplir les produits existants en mode édition
function populateExistingProducts() {
    console.log('🔄 Pré-remplissage des produits existants...');

    @if(isset($orderProducts) && !empty($orderProducts))
        const orderProducts = @json($orderProducts);
        console.log('📦 Produits de la commande:', orderProducts);

        // Supprimer le premier produit vide s'il existe
        const firstProductItem = document.querySelector('.product-item');
        if (firstProductItem) {
            firstProductItem.remove();
        }

        // Ajouter chaque produit existant
        orderProducts.forEach((productData, index) => {
            console.log(`📦 Ajout du produit #${index + 1}:`, productData);

            // Ajouter un nouveau produit
            addProduct();

            // Récupérer le produit ajouté
            const productItems = document.querySelectorAll('.product-item');
            const currentProductItem = productItems[productItems.length - 1];

            // Pré-remplir les champs
            const productSelect = currentProductItem.querySelector('.product-select');
            const sizeSelect = currentProductItem.querySelector('.size-select');
            const quantityInput = currentProductItem.querySelector('.quantity-input');
            const prixVenteInput = currentProductItem.querySelector('.prix-vente-input');

            if (productSelect && sizeSelect && quantityInput && prixVenteInput) {
                // Sélectionner le produit
                productSelect.value = productData.product_id;
                console.log(`✅ Produit sélectionné: ${productData.product_id}`);

                // Déclencher l'événement change pour charger les tailles et autres données
                productSelect.dispatchEvent(new Event('change'));

                // Attendre que les tailles soient chargées puis sélectionner la taille
                setTimeout(() => {
                    if (sizeSelect) {
                        sizeSelect.value = productData.taille;
                        console.log(`✅ Taille sélectionnée: ${productData.taille}`);
                    }

                    if (quantityInput) {
                        quantityInput.value = productData.qty;
                        console.log(`✅ Quantité définie: ${productData.qty}`);
                    }

                    if (prixVenteInput) {
                        prixVenteInput.value = productData.prix_vente_client;
                        console.log(`✅ Prix de vente défini: ${productData.prix_vente_client}`);
                    }

                    // Recalculer les totaux avec protection
                    safeCalculateTotals();
                }, 500);
            }
        });

        // Calculer le prix d'achat pour tous les produits après le pré-remplissage
        setTimeout(() => {
            const productItems = document.querySelectorAll('.product-item');
            productItems.forEach(item => {
                calculatePurchasePrice(item);
            });
        }, 1500);

        console.log('✅ Pré-remplissage terminé');
    @endif
}

// Fonction pour ajouter les notes manquantes aux produits
function ensureAllProductNotesAreVisible() {
    console.log('🔍 Vérification des notes pour tous les produits...');

    const productItems = document.querySelectorAll('.product-item');
    productItems.forEach((item, index) => {
        const productSelect = item.querySelector('.product-select');
        const sizeSelect = item.querySelector('.size-select');
        const productTitle = item.querySelector('h4')?.textContent || `Produit #${index + 1}`;

        // Si un produit est sélectionné mais qu'il n'y a pas de note d'information
        if (productSelect && productSelect.value && sizeSelect) {
            const existingNotes = sizeSelect.parentElement.querySelectorAll('p.text-xs');
            if (existingNotes.length === 0) {
                console.log(`⚠️ ${productTitle} manque de note d'information, ajout...`);
                productSelect.dispatchEvent(new Event('change'));
            } else {
                console.log(`✅ ${productTitle} a déjà une note d'information`);
            }
        }
    });
}

// Fonction pour forcer la réinitialisation des calculs
function forceRecalculate() {
    console.log('🔄 Forçage du recalcul des totaux...');

    const productItems = document.querySelectorAll('.product-item');
    console.log(`Produits trouvés: ${productItems.length}`);

    productItems.forEach((item, index) => {
        console.log(`\n--- Produit #${index + 1} ---`);

        // Récupérer tous les éléments
        const productSelect = item.querySelector('.product-select');
        const sizeSelect = item.querySelector('.size-select');
        const quantityInput = item.querySelector('.quantity-input');
        const prixVenteInput = item.querySelector('.prix-vente-input');
        const prixAchatDisplay = item.querySelector('.prix-achat-display');
        const margeProduitDisplay = item.querySelector('.marge-produit-display');

        console.log(`Produit sélectionné: ${productSelect ? productSelect.value : 'NON TROUVÉ'}`);
        console.log(`Taille sélectionnée: ${sizeSelect ? sizeSelect.value : 'NON TROUVÉ'}`);
        console.log(`Quantité: ${quantityInput ? quantityInput.value : 'NON TROUVÉ'}`);
        console.log(`Prix de vente: ${prixVenteInput ? prixVenteInput.value : 'NON TROUVÉ'}`);
        console.log(`Prix d'achat: ${prixAchatDisplay ? prixAchatDisplay.value : 'NON TROUVÉ'}`);
        console.log(`Marge produit: ${margeProduitDisplay ? margeProduitDisplay.value : 'NON TROUVÉ'}`);

        // Si un produit est sélectionné mais pas de taille, forcer la sélection
        if (productSelect && productSelect.value && (!sizeSelect || !sizeSelect.value || sizeSelect.value === '')) {
            console.log('⚠️ Produit sélectionné mais pas de taille, réinitialisation...');
            try {
                productSelect.dispatchEvent(new Event('change'));
            } catch (error) {
                console.error('❌ Erreur lors de la réinitialisation:', error);
            }
        }

        // Recalculer la marge pour ce produit seulement si tous les éléments sont présents
        if (prixAchatDisplay && prixVenteInput && quantityInput && margeProduitDisplay) {
            try {
                // Calculer d'abord le prix d'achat selon la quantité
                calculatePurchasePrice(item);
                // Puis calculer la marge
                calculateProductMargin(item);
            } catch (error) {
                console.error(`❌ Erreur lors du calcul de la marge pour le produit #${index + 1}:`, error);
            }
        } else {
            console.log(`⚠️ Impossible de calculer la marge pour le produit #${index + 1} - éléments manquants`);
        }
    });

    // Recalculer les totaux avec protection
    try {
        safeCalculateTotals();
    } catch (error) {
        console.error('❌ Erreur lors du calcul des totaux:', error);
    }

    console.log('✅ Recalcul forcé terminé');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Initialisation du formulaire de commande ===');

    // Charger la configuration de livraison
    loadDeliveryConfig();

    // Debug des éléments au chargement
    setTimeout(() => {
        debugElements();
    }, 100);

    // Événement de changement de ville
    const villeSelect = document.getElementById('villeSelect');
    if (villeSelect) {
        villeSelect.addEventListener('change', updateDeliveryPrice);
        console.log('✅ Événement ville ajouté');
    } else {
        console.error('❌ Élément villeSelect non trouvé');
    }

    // Événement du bouton d'ajout de produit
    const addProductBtn = document.getElementById('addProductBtn');
    if (addProductBtn) {
        addProductBtn.addEventListener('click', addProduct);
        console.log('✅ Événement ajout produit ajouté');
    } else {
        console.error('❌ Élément addProductBtn non trouvé');
    }

        // Configurer les événements pour le premier produit
    const firstProductItem = document.querySelector('.product-item');
    if (firstProductItem) {
        setupProductEvents(firstProductItem);
        console.log('✅ Événements du premier produit configurés');

        // Si le premier produit a déjà un produit sélectionné, déclencher l'événement change
        const firstProductSelect = firstProductItem.querySelector('.product-select');
        if (firstProductSelect && firstProductSelect.value) {
            console.log('🔄 Produit déjà sélectionné dans le premier produit, mise à jour des tailles...');
            // Forcer la mise à jour complète des tailles
            setTimeout(() => {
                firstProductSelect.dispatchEvent(new Event('change'));
                // Calculer le prix d'achat après la mise à jour
                calculatePurchasePrice(firstProductItem);
            }, 100);
        }
    } else {
        console.error('❌ Premier produit non trouvé');
    }

                // Initialiser les calculs
        setTimeout(() => {
            safeCalculateTotals();
            console.log('✅ Calculs initiaux effectus');

        // Forcer un recalcul après un délai supplémentaire
        setTimeout(() => {
            forceRecalculate();

            // Vérifier que tous les produits ont leurs notes
            setTimeout(() => {
                ensureAllProductNotesAreVisible();
            }, 200);
        }, 500);
    }, 200);

    // Si on est en mode édition, pré-remplir les produits existants
    @if(isset($orderProducts) && !empty($orderProducts))
        console.log('🔄 Mode édition détecté, pré-remplissage des produits...');
        setTimeout(() => {
            populateExistingProducts();
        }, 1000);
    @endif

    console.log('=== Initialisation terminée ===');
});

// Fonction pour rafraîchir l'affichage du stock après une commande
function refreshStockDisplay() {
    console.log('🔄 Rafraîchissement de l\'affichage du stock...');

    // Recharger les données des produits depuis le serveur
    const productSelects = document.querySelectorAll('.product-select');

    productSelects.forEach(async (productSelect, index) => {
        if (productSelect.value) {
            const productId = productSelect.value;
            console.log(`🔄 Rafraîchissement du stock pour le produit #${index + 1} (ID: ${productId})`);

            try {
                // Faire une requête AJAX pour récupérer les données mises à jour
                const response = await fetch(`/api/products/${productId}/stock`);
                if (response.ok) {
                    const productData = await response.json();
                    console.log('✅ Données de stock mises à jour:', productData);

                    // Mettre à jour l'affichage des couleurs avec le nouveau stock
                    updateColorOptions(productSelect, productData);
                } else {
                    console.warn('⚠️ Impossible de récupérer les données de stock mises à jour');
                }
            } catch (error) {
                console.error('❌ Erreur lors du rafraîchissement du stock:', error);
            }
        }
    });
}

// Fonction pour mettre à jour les options de couleur avec le nouveau stock
function updateColorOptions(productSelect, productData) {
    const productItem = productSelect.closest('.product-item');
    const colorSelect = productItem.querySelector('.color-select');

    if (!colorSelect) return;

    // Vider les options existantes
    colorSelect.innerHTML = '<option value="">Sélectionnez une couleur</option>';

    // Récupérer le stock mis à jour
    const stockCouleurs = productData.stock_couleurs || [];
    const couleurs = [];

    if (Array.isArray(stockCouleurs) && stockCouleurs.length > 0) {
        couleurs = stockCouleurs.map(sc => ({
            name: sc.name,
            quantity: sc.quantity || 0
        }));
    }

    // Afficher les couleurs avec le nouveau stock
    if (couleurs.length > 0) {
        let couleursDisponibles = 0;

        couleurs.forEach(couleur => {
            const option = document.createElement('option');
            option.value = couleur.name;

            const stockQuantity = parseInt(couleur.quantity) || 0;

            if (stockQuantity <= 0) {
                // Couleur en rupture de stock
                option.textContent = `${couleur.name} (en stock : 0)`;
                option.setAttribute('data-stock', stockQuantity);
                option.disabled = true;
                option.style.color = '#999';
                option.style.fontStyle = 'italic';
                console.log(`⚠️ Couleur en rupture mise à jour: ${couleur.name} (stock: ${stockQuantity})`);
            } else {
                // Couleur disponible
                couleursDisponibles++;
                option.textContent = `${couleur.name} (en stock : ${stockQuantity})`;
                option.setAttribute('data-stock', stockQuantity);
                console.log(`🎨 Couleur disponible mise à jour: ${couleur.name} (stock: ${stockQuantity})`);
            }

            colorSelect.appendChild(option);
        });

        console.log(`✅ Stock rafraîchi: ${couleurs.length} couleurs (${couleursDisponibles} disponibles)`);

        // Si aucune couleur disponible, afficher un message d'avertissement
        if (couleursDisponibles === 0) {
            const warningOption = document.createElement('option');
            warningOption.value = '';
            warningOption.textContent = '⚠️ Toutes les couleurs sont en rupture de stock';
            warningOption.disabled = true;
            warningOption.style.color = '#ff6b6b';
            warningOption.style.fontWeight = 'bold';
            colorSelect.insertBefore(warningOption, colorSelect.firstChild);
        }
    }
}
</script>

<style>
    /* Styles pour les boutons d'action */
    .add-product-btn, .edit-product-btn, .remove-product-btn {
        transition: all 0.2s ease-in-out;
        padding: 0.5rem;
        border-radius: 0.5rem;
    }

    .add-product-btn:hover {
        background-color: rgba(34, 197, 94, 0.1);
        transform: scale(1.1);
    }

    .edit-product-btn:hover {
        background-color: rgba(59, 130, 246, 0.1);
        transform: scale(1.1);
    }

    .remove-product-btn:hover {
        background-color: rgba(239, 68, 68, 0.1);
        transform: scale(1.1);
    }

    /* Animation pour la surbrillance de modification */
    .ring-2.ring-blue-500 {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>


