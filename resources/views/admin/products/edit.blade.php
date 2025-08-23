@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Modifier le Produit</h1>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>

            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du produit -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Produit *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
                        <select name="categorie_id" id="categorie_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('categorie_id', $product->categorie_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('categorie_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Couleurs -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Couleurs *</label>
                        <div class="space-y-4">
                            <!-- Couleurs prédéfinies -->
                            <div>
                                <p class="text-xs text-gray-600 mb-3 flex items-center">
                                    <i class="fas fa-palette mr-2 text-blue-600"></i>
                                    Couleurs prédéfinies : <span id="selectedColorsCount" class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">0 sélectionnée</span>
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @php
                                        $predefinedColors = [
                                            'Rouge' => '#ff0000', 'Vert' => '#00ff00', 'Bleu' => '#0000ff', 'Jaune' => '#ffff00',
                                            'Noir' => '#000000', 'Blanc' => '#ffffff', 'Orange' => '#ffa500', 'Violet' => '#800080',
                                            'Rose' => '#ffc0cb', 'Marron' => '#a52a2a', 'Gris' => '#808080', 'Beige' => '#f5f5dc'
                                        ];
                                        // Récupérer les couleurs depuis le produit
                                        $rawColors = is_string($product->couleur) ? json_decode($product->couleur, true) ?? [] : (is_array($product->couleur) ? $product->couleur : []);

                                        // Extraire les noms de couleurs pour les comparaisons
                                        $currentColors = [];
                                        $customColors = [];

                                        foreach ($rawColors as $color) {
                                            if (is_array($color) && isset($color['name'])) {
                                                $colorName = $color['name'];
                                                $colorHex = $color['hex'] ?? null;
                                                $currentColors[] = $colorName;
                                                if (!in_array($colorName, array_keys($predefinedColors))) {
                                                    $customColors[] = ['name' => $colorName, 'hex' => $colorHex];
                                                }
                                            } elseif (is_string($color)) {
                                                $currentColors[] = $color;
                                                if (!in_array($color, array_keys($predefinedColors))) {
                                                    $customColors[] = ['name' => $color, 'hex' => null];
                                                }
                                            }
                                        }
                                    @endphp
                                    @foreach($predefinedColors as $colorName => $colorHex)
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300 color-card" data-color-name="{{ $colorName }}">
                                            <div class="flex items-center justify-between mb-3">
                                                <label class="flex items-center space-x-3 cursor-pointer flex-1">
                                                    <input type="checkbox" name="couleurs_predefinies[]" value="{{ $colorName }}"
                                                           @checked(in_array($colorName, old('couleurs_predefinies', $currentColors)))
                                                           data-hex="{{ $colorHex }}"
                                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 color-checkbox"
                                                           onchange="updateColorHex(this)">
                                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm color-preview" style="background-color: {{ $colorHex }}"></div>
                                                    <span class="text-sm font-medium text-gray-700 color-name">{{ $colorName }}</span>
                                                </label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <label class="text-xs font-medium text-gray-600">Stock:</label>
                                                <input type="number" name="stock_couleur_{{ $loop->index }}"
                                                       placeholder="0" min="0"
                                                       class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 stock-input"
                                                       value="{{ old('stock_couleur_' . $loop->index, 0) }}"
                                                       onchange="updateSelectedColorsCount()">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Interface d'ajout de couleur personnalisée -->
                            <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm">
                                <p class="text-sm font-semibold text-blue-800 mb-4 flex items-center">
                                    <i class="fas fa-palette mr-2 text-blue-600"></i>
                                    Ajouter une couleur personnalisée
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                    <div class="flex flex-col space-y-2">
                                        <label class="text-xs font-medium text-blue-700">Sélectionner la couleur</label>
                                        <div class="flex items-center space-x-3">
                                            <input type="color" id="customColorPicker" value="#ff6b6b"
                                                   class="w-14 h-12 border-2 border-gray-300 rounded-lg cursor-pointer shadow-sm">
                                            <div id="colorPreview" class="w-10 h-10 rounded-full border-2 border-gray-300 shadow-sm"
                                                 style="background-color: #ff6b6b;"></div>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="text-xs font-medium text-blue-700 mb-2 block">Nom de la couleur</label>
                                        <input type="text" id="customColorName" placeholder="Ex: Corail, Indigo, Marine..."
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" onclick="addCustomColor()"
                                                class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 hover:shadow-md font-medium">
                                            <i class="fas fa-plus mr-2"></i>Ajouter
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="flex items-center space-x-2 text-xs text-gray-600">
                                        <input type="checkbox" id="forceCustomName" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                        <span>Forcer l'utilisation du nom personnalisé (ignorer l'auto-remplissage)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Couleurs personnalisées ajoutées -->
                            <div id="customColorsContainer" class="space-y-3">
                                @foreach($customColors as $customColor)
                                    <div class="custom-color-item bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center space-x-3">
                                                <span class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm"
                                                      style="background-color: {{ $customColor['hex'] ?: \App\Helpers\ColorHelper::generateColorFromName($customColor['name']) }}"></span>
                                                <span class="text-sm font-medium text-gray-700">{{ $customColor['name'] }}</span>
                                            </div>
                                            <button type="button" onclick="removeCustomColor(this)"
                                                    class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-full transition-colors">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <label class="text-xs font-medium text-gray-600">Stock:</label>
                                            <input type="number" name="stock_couleur_custom_{{ $loop->index }}"
                                                   placeholder="0" min="0"
                                                   class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                                                   value="0">
                                        </div>
                                        <input type="hidden" name="couleurs_personnalisees[]" value="{{ $customColor['name'] }}">
                                    </div>
                                @endforeach
                            </div>

                            <!-- Résumé des couleurs sélectionnées -->
                            <div id="selectedColorsSummary" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg" style="display: none;">
                                <h4 class="text-sm font-semibold text-green-800 mb-2 flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Couleurs sélectionnées :
                                </h4>
                                <div id="selectedColorsList" class="flex flex-wrap gap-2">
                                    <!-- Les couleurs sélectionnées seront affichées ici -->
                                </div>
                            </div>

                            <!-- Champ caché pour toutes les couleurs combinées -->
                            <div id="couleursCombinedContainer">
                                <!-- Les inputs couleurs[] seront créés ici dynamiquement -->
                            </div>
                        </div>
                        @error('couleurs')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tailles -->
                    <div class="md:col-span-2" id="taillesSection">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tailles Disponibles <span id="taillesRequired" class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '38', '40', '42', '44', '46', '48', '50', '52'];
                                $currentSizes = is_string($product->tailles)
                                    ? (json_decode($product->tailles, true) ?? [])
                                    : (is_array($product->tailles) ? $product->tailles : []);
                            @endphp
                            @foreach($sizes as $size)
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="tailles[]" value="{{ $size }}"
                                           @checked(in_array($size, old('tailles', $currentSizes)))
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 taille-checkbox">
                                    <span class="text-sm text-gray-700">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('tailles')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix Admin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix Admin (MAD) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">MAD</span>
                            <input type="number" name="prix_admin" value="{{ old('prix_admin', $product->prix_admin) }}" step="0.01" min="0" required
                                   class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('prix_admin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix de Vente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix de Vente (MAD) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">MAD</span>
                            <input type="number" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente) }}" step="0.01" min="0" required
                                   class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('prix_vente')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantité en Stock Total (Calculée automatiquement) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en Stock Total</label>
                        <input type="number" id="stockTotal" value="0" min="0" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-semibold">
                        <p class="text-xs text-gray-500 mt-1">💡 Calculé automatiquement : somme des stocks de toutes les couleurs</p>
                        <input type="hidden" name="quantite_stock" id="stockTotalHidden" value="0">
                        @error('quantite_stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image du Produit</label>
                        @if($product->image)
                            <div class="mb-2">
                                <img src="{{ $product->image }}" alt="Image actuelle" class="w-20 h-20 object-cover rounded-lg border">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Mettre à Jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion des couleurs personnalisées
let customColorCounter = 0;

// Ajouter une couleur personnalisée
function addCustomColor() {
    const colorPicker = document.getElementById('customColorPicker');
    const colorNameInput = document.getElementById('customColorName');
    const container = document.getElementById('customColorsContainer');

    if (!colorNameInput.value.trim()) {
        alert('Veuillez entrer un nom pour la couleur');
        return;
    }

    const colorName = colorNameInput.value.trim();
    const colorHex = colorPicker.value;

    console.log('=== DEBUG AJOUT COULEUR ===');
    console.log('Nom de la couleur (brut):', colorNameInput.value);
    console.log('Nom de la couleur (trim):', colorName);
    console.log('Longueur du nom:', colorName.length);
    console.log('Couleur hex:', colorHex);
    console.log('Nom encodé en JSON:', JSON.stringify(colorName));
    console.log('==========================');

    // Créer l'élément de couleur personnalisée
    const colorItem = document.createElement('div');
    colorItem.className = 'custom-color-item bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300 mb-3';
    colorItem.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-3">
                <span class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm" style="background-color: ${colorHex}"></span>
                <span class="text-sm font-medium text-gray-700">${colorName}</span>
            </div>
            <button type="button" onclick="removeCustomColor(this)" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-full transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex items-center space-x-2">
            <label class="text-xs font-medium text-gray-600">Stock:</label>
            <input type="number" name="stock_couleur_custom_${customColorCounter}"
                   placeholder="0" min="0"
                   class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                   value="0">
        </div>
        <input type="hidden" name="couleurs_personnalisees[]" value="${colorName}">
    `;

    container.appendChild(colorItem);

    console.log('Élément de couleur créé:', colorItem.outerHTML); // Debug de l'élément créé
    console.log('Couleur ajoutée au container:', container.children.length); // Debug du nombre d'éléments

    // Réinitialiser seulement le nom, garder la couleur sélectionnée
    colorNameInput.value = '';
    // Ne pas réinitialiser le color picker pour garder la couleur visible

    // Mettre à jour le champ couleurs combinées
    updateCombinedColors();

    // Ajouter l'écouteur d'événements pour le stock
    const stockInput = colorItem.querySelector('input[name^="stock_couleur_custom_"]');
    if (stockInput) {
        stockInput.addEventListener('input', calculateTotalStock);
    }

    // Recalculer le stock total
    calculateTotalStock();

    customColorCounter++;
}

// Fonction pour gérer les couleurs hex des couleurs prédéfinies
function updateColorHex(checkbox) {
    const hexValue = checkbox.getAttribute('data-hex');
    const existingHexInput = document.querySelector(`input[type="hidden"][name="couleurs_hex[]"][value="${hexValue}"]`);

    if (checkbox.checked && !existingHexInput) {
        // Ajouter l'input hex si la couleur est cochée
        const hexInput = document.createElement('input');
        hexInput.type = 'hidden';
        hexInput.name = 'couleurs_hex[]';
        hexInput.value = hexValue;
        hexInput.setAttribute('data-color', checkbox.value);
        checkbox.parentElement.appendChild(hexInput);
    } else if (!checkbox.checked && existingHexInput) {
        // Supprimer l'input hex si la couleur est décochée
        existingHexInput.remove();
    }

    // Mettre à jour l'affichage des couleurs sélectionnées
    updateSelectedColorsCount();
    updateSelectedColorsSummary();

    // Mettre à jour le champ couleurs combinées
    updateCombinedColors();
}

// Supprimer une couleur personnalisée
function removeCustomColor(button) {
    button.closest('.custom-color-item').remove();
    // Mettre à jour le champ couleurs combinées
    updateCombinedColors();

    // Mettre à jour l'affichage des couleurs sélectionnées
    updateSelectedColorsCount();
    updateSelectedColorsSummary();
}

// Mettre à jour le champ couleurs combinées
function updateCombinedColors() {
    // Mapping des couleurs prédéfinies vers leurs valeurs hex
    const predefinedColorsHex = {
        'Rouge': '#ff0000', 'Vert': '#00ff00', 'Bleu': '#0000ff', 'Jaune': '#ffff00',
        'Noir': '#000000', 'Blanc': '#ffffff', 'Orange': '#ffa500', 'Violet': '#800080',
        'Rose': '#ffc0cb', 'Marron': '#a52a2a', 'Gris': '#808080', 'Beige': '#f5f5dc'
    };

    const selectedPredefinedColors = Array.from(document.querySelectorAll('input[name="couleurs_predefinies[]"]:checked'))
        .map(input => input.value);

    const customColors = Array.from(document.querySelectorAll('#customColorsContainer input[name="couleurs_personnalisees[]"]'))
        .map(input => input.value);

    const allColors = [...selectedPredefinedColors, ...customColors];

    // Vider le container des couleurs combinées
    const container = document.getElementById('couleursCombinedContainer');
    if (container) {
        container.innerHTML = '';

        // Créer un input caché pour chaque couleur avec son hex
        allColors.forEach(color => {
            // Input pour le nom de la couleur
            const colorInput = document.createElement('input');
            colorInput.type = 'hidden';
            colorInput.name = 'couleurs[]';
            colorInput.value = color;
            container.appendChild(colorInput);

            // Input pour la valeur hex
            const hexInput = document.createElement('input');
            hexInput.type = 'hidden';
            hexInput.name = 'couleurs_hex[]';

            // Déterminer la valeur hex
            if (predefinedColorsHex[color]) {
                // Couleur prédéfinie
                hexInput.value = predefinedColorsHex[color];
            } else {
                // Couleur personnalisée - essayer de récupérer depuis l'élément personnalisé
                const customColorElement = Array.from(document.querySelectorAll('#customColorsContainer .custom-color-item'))
                    .find(el => el.querySelector('input[name="couleurs_personnalisees[]"]')?.value === color);

                if (customColorElement) {
                    const colorSpan = customColorElement.querySelector('span[style*="background-color"]');
                    if (colorSpan) {
                        const style = colorSpan.getAttribute('style');
                        const hexMatch = style.match(/background-color:\s*([#][0-9a-fA-F]{6})/);
                        hexInput.value = hexMatch ? hexMatch[1] : '#cccccc';
                    } else {
                        hexInput.value = '#cccccc';
                    }
                } else {
                    hexInput.value = '#cccccc';
                }
            }

            container.appendChild(hexInput);
        });

        console.log('Champs couleurs mis à jour en temps réel:', allColors);
        console.log('Nombre d\'inputs créés:', container.children.length);
    }
}

// Fonction pour mettre à jour la prévisualisation et le nom de couleur
function updateColorPreview() {
    const colorPicker = document.getElementById('customColorPicker');
    const colorNameInput = document.getElementById('customColorName');
    const colorPreview = document.getElementById('colorPreview');

    if (!colorPicker || !colorPreview) return;

    const selectedColor = colorPicker.value;

    // Mettre à jour la prévisualisation en temps réel
    colorPreview.style.backgroundColor = selectedColor;

    // Auto-remplir le nom SEULEMENT si le champ est vide
    const colorNames = {
        '#ff0000': 'Rouge', '#00ff00': 'Vert', '#0000ff': 'Bleu', '#ffff00': 'Jaune',
        '#ff00ff': 'Magenta', '#00ffff': 'Cyan', '#000000': 'Noir', '#ffffff': 'Blanc',
        '#ffa500': 'Orange', '#800080': 'Violet', '#ffc0cb': 'Rose', '#a52a2a': 'Marron',
        '#ff4500': 'Orange-Rouge', '#32cd32': 'Lime', '#4169e1': 'Royal Blue', '#ffd700': 'Or'
    };

    // Vérifier si l'utilisateur veut forcer l'utilisation du nom personnalisé
    const forceCustomName = document.getElementById('forceCustomName')?.checked || false;

    // Ne remplir que si le champ est vide ET que l'utilisateur n'a pas forcé le nom personnalisé
    if (colorNames[selectedColor] && (!colorNameInput.value.trim() || colorNameInput.value.trim() === '') && !forceCustomName) {
        colorNameInput.value = colorNames[selectedColor];
        console.log('Nom auto-rempli avec:', colorNames[selectedColor]);
    } else if (colorNames[selectedColor] && forceCustomName) {
        console.log('Nom personnalisé forcé, auto-remplissage ignoré');
    } else if (colorNames[selectedColor]) {
        console.log('Nom personnalisé conservé:', colorNameInput.value.trim());
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Formulaire d\'édition des couleurs initialisé');

    // Initialiser la prévisualisation avec la couleur par défaut du color picker
    const colorPicker = document.getElementById('customColorPicker');
    const colorPreview = document.getElementById('colorPreview');
    if (colorPicker && colorPreview) {
        colorPreview.style.backgroundColor = colorPicker.value;

        // Ajouter les événements pour le color picker
        colorPicker.addEventListener('change', updateColorPreview);
        colorPicker.addEventListener('input', updateColorPreview);
    }

    // Vérifier qu'au moins une couleur est sélectionnée
    const selectedColors = document.querySelectorAll('input[name="couleurs_predefinies[]"]:checked');
    const customColors = document.querySelectorAll('#customColorsContainer input[name="couleurs_personnalisees[]"]');

    if (selectedColors.length === 0 && customColors.length === 0) {
        console.log('Aucune couleur sélectionnée, sélection automatique de la première couleur prédéfinie');
        const firstCheckbox = document.querySelector('input[name="couleurs_predefinies[]"]');
        if (firstCheckbox) {
            firstCheckbox.checked = true;
        }
    }

    // Ajouter des événements pour les checkboxes des couleurs prédéfinies
    const predefinedCheckboxes = document.querySelectorAll('input[name="couleurs_predefinies[]"]');
    predefinedCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCombinedColors);
    });

    // Initialiser le champ couleurs combinées
    updateCombinedColors();

    // Gestion conditionnelle des tailles selon la catégorie
    const categorieSelect = document.getElementById('categorie_id');
    const taillesSection = document.getElementById('taillesSection');
    const taillesRequired = document.getElementById('taillesRequired');
    const tailleCheckboxes = document.querySelectorAll('.taille-checkbox');

    console.log('Elements trouvés:');
    console.log('categorieSelect:', categorieSelect);
    console.log('taillesSection:', taillesSection);
    console.log('tailleCheckboxes:', tailleCheckboxes.length);

            function toggleTaillesSection() {
        if (categorieSelect && categorieSelect.value) {
            const selectedOption = categorieSelect.options[categorieSelect.selectedIndex];
            const categoryText = selectedOption.text.toLowerCase();
            const isAccessoire = categoryText.includes('accessoire');

            console.log('Catégorie sélectionnée:', selectedOption.text);
            console.log('Texte en minuscules:', categoryText);
            console.log('Est accessoire:', isAccessoire);

            if (isAccessoire) {
                // Masquer la section des tailles pour les accessoires
                taillesSection.style.display = 'none';
                taillesRequired.style.display = 'none';

                // Décocher toutes les tailles et retirer l'attribut name pour éviter l'envoi
                tailleCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.removeAttribute('name');
                    checkbox.disabled = true;
                });
            } else {
                // Afficher la section des tailles pour les autres catégories
                taillesSection.style.display = 'block';
                taillesRequired.style.display = 'inline';

                // Réactiver les inputs et remettre l'attribut name
                tailleCheckboxes.forEach(checkbox => {
                    checkbox.setAttribute('name', 'tailles[]');
                    checkbox.disabled = false;
                });
            }
        }
    }

    // Écouter les changements de catégorie
    if (categorieSelect) {
        categorieSelect.addEventListener('change', toggleTaillesSection);
        // Appliquer au chargement initial
        toggleTaillesSection();
    }

    // Ajouter les écouteurs d'événements pour les inputs de stock des couleurs prédéfinies
    const predefinedStockInputs = document.querySelectorAll('input[name^="stock_couleur_"]');
    predefinedStockInputs.forEach(input => {
        input.addEventListener('input', calculateTotalStock);
    });

    // Ajouter les écouteurs d'événements pour les checkboxes des couleurs prédéfinies
    const predefinedColorCheckboxes = document.querySelectorAll('input[name="couleurs_predefinies[]"]');
    predefinedColorCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotalStock);
    });

    // Initialiser le stock total avec la valeur existante du produit
    const initialStock = {{ $product->quantite_stock ?? 0 }};
    const stockTotal = document.getElementById('stockTotal');
    const stockTotalHidden = document.getElementById('stockTotalHidden');

    if (stockTotal) stockTotal.value = initialStock;
    if (stockTotalHidden) stockTotalHidden.value = initialStock;

    // Calculer le stock total initial
    calculateTotalStock();
});

// Fonction pour calculer le stock total
function calculateTotalStock() {
    let total = 0;

    // Calculer le stock des couleurs prédéfinies
    const predefinedStockInputs = document.querySelectorAll('input[name^="stock_couleur_"]');

    predefinedStockInputs.forEach((input) => {
        // Trouver la checkbox de couleur en remontant jusqu'au conteneur principal
        let colorContainer = input.parentElement;
        let checkbox = null;

        // Remonter jusqu'à trouver le conteneur avec la checkbox
        while (colorContainer && !checkbox) {
            checkbox = colorContainer.querySelector('input[name="couleurs_predefinies[]"]');
            if (!checkbox) {
                colorContainer = colorContainer.parentElement;
            }
        }

        if (checkbox && checkbox.checked) {
            const stockValue = parseInt(input.value) || 0;
            total += stockValue;
        }
    });

    // Calculer le stock des couleurs personnalisées
    const customStockInputs = document.querySelectorAll('input[name^="stock_couleur_custom_"]');
    customStockInputs.forEach((input) => {
        const stockValue = parseInt(input.value) || 0;
        total += stockValue;
    });

    // Mettre à jour l'affichage
    const stockTotal = document.getElementById('stockTotal');
    const stockTotalHidden = document.getElementById('stockTotalHidden');

    if (stockTotal) {
        stockTotal.value = total;
        // Forcer la mise à jour de l'affichage
        stockTotal.dispatchEvent(new Event('input', { bubbles: true }));
        stockTotal.dispatchEvent(new Event('change', { bubbles: true }));
        // Forcer le rafraîchissement visuel
        stockTotal.style.backgroundColor = '#ffffff';
        setTimeout(() => {
            stockTotal.style.backgroundColor = '#f9fafb';
        }, 100);
    }

    if (stockTotalHidden) {
        stockTotalHidden.value = total;
    }

    return total;
}

// Préparation des données avant soumission
function prepareFormData() {
    console.log('Préparation des données du formulaire...');

    const selectedPredefinedColors = Array.from(document.querySelectorAll('input[name="couleurs_predefinies[]"]:checked'))
        .map(input => input.value);

    const customColors = Array.from(document.querySelectorAll('#customColorsContainer input[name="couleurs_personnalisees[]"]'))
        .map(input => input.value);

    // Combiner toutes les couleurs
    const allColors = [...selectedPredefinedColors, ...customColors];

    console.log('Couleurs prédéfinies sélectionnées:', selectedPredefinedColors);
    console.log('Couleurs personnalisées:', customColors);
    console.log('Toutes les couleurs combinées:', allColors);

    // Mettre à jour les couleurs combinées une dernière fois avant soumission
    updateCombinedColors();

    // Vérifier qu'il y a au moins une couleur
    const couleursInputs = document.querySelectorAll('input[name="couleurs[]"]');
    const hasColors = couleursInputs.length > 0;

    console.log('Nombre de couleurs avant soumission:', couleursInputs.length);
    couleursInputs.forEach((input, index) => {
        console.log(`Couleur ${index + 1}:`, input.value);
    });

    return hasColors;
}

    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('Soumission du formulaire...');

        if (!prepareFormData()) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une couleur');
            return;
        }

        // Vérifier que le champ couleurs a été créé
        const couleursInput = document.querySelector('input[name="couleurs"]');
        if (couleursInput) {
            console.log('Champ couleurs créé avec valeur:', couleursInput.value);
        } else {
            console.error('Champ couleurs non trouvé !');
        }
    });

    // Fonction pour mettre à jour le compteur de couleurs sélectionnées
    function updateSelectedColorsCount() {
        const selectedPredefinedColors = document.querySelectorAll('input[name="couleurs_predefinies[]"]:checked');
        const customColors = document.querySelectorAll('#customColorsContainer input[name="couleurs_personnalisees[]"]');
        const totalColors = selectedPredefinedColors.length + customColors.length;

        const countElement = document.getElementById('selectedColorsCount');
        if (countElement) {
            countElement.textContent = `${totalColors} sélectionnée${totalColors > 1 ? 's' : ''}`;
        }
    }

    // Fonction pour afficher le résumé des couleurs sélectionnées
    function updateSelectedColorsSummary() {
        const selectedPredefinedColors = document.querySelectorAll('input[name="couleurs_predefinies[]"]:checked');
        const customColors = document.querySelectorAll('#customColorsContainer input[name="couleurs_personnalisees[]"]');
        const summaryElement = document.getElementById('selectedColorsSummary');
        const listElement = document.getElementById('selectedColorsList');

        if (selectedPredefinedColors.length === 0 && customColors.length === 0) {
            summaryElement.style.display = 'none';
            return;
        }

        summaryElement.style.display = 'block';
        listElement.innerHTML = '';

        // Ajouter les couleurs prédéfinies sélectionnées
        selectedPredefinedColors.forEach(checkbox => {
            const colorName = checkbox.value;
            const colorCard = checkbox.closest('.color-card');
            const colorPreview = colorCard.querySelector('.color-preview');
            const hexColor = colorPreview.style.backgroundColor;
            const stockInput = colorCard.querySelector('.stock-input');
            const stockValue = stockInput ? stockInput.value : '0';

            const colorTag = document.createElement('div');
            colorTag.className = 'flex items-center space-x-2 px-3 py-2 bg-white border border-green-300 rounded-lg shadow-sm';
            colorTag.innerHTML = `
                <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: ${hexColor}"></div>
                <span class="text-sm font-medium text-green-800">${colorName}</span>
                <span class="text-xs text-green-600">(Stock: ${stockValue})</span>
            `;

            listElement.appendChild(colorTag);
        });

        // Ajouter les couleurs personnalisées
        customColors.forEach(input => {
            const colorName = input.value;
            const customColorItem = input.closest('.custom-color-item');
            const colorPreview = customColorItem.querySelector('span[style*="background-color"]');
            const hexColor = colorPreview.style.backgroundColor;
            const stockInput = customColorItem.querySelector('input[type="number"]');
            const stockValue = stockInput ? stockInput.value : '0';

            const colorTag = document.createElement('div');
            colorTag.className = 'flex items-center space-x-2 px-3 py-2 bg-white border border-blue-300 rounded-lg shadow-sm';
            colorTag.innerHTML = `
                <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: ${hexColor}"></div>
                <span class="text-sm font-medium text-blue-800">${colorName}</span>
                <span class="text-xs text-blue-600">(Stock: ${stockValue})</span>
            `;

            listElement.appendChild(colorTag);
        });
    }

    // Initialiser l'affichage au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        updateSelectedColorsCount();
        updateSelectedColorsSummary();
    });
</script>
@endsection


