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
                                <p class="text-xs text-gray-600 mb-3">Couleurs prédéfinies :</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
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
                                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                                            <input type="checkbox" name="couleurs_predefinies[]" value="{{ $colorName }}"
                                                   @checked(in_array($colorName, old('couleurs_predefinies', $currentColors)))
                                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="w-4 h-4 rounded-full border border-gray-300"
                                                  style="background-color: {{ $colorHex }}"></span>
                                            <span class="text-sm text-gray-700">{{ $colorName }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Color picker pour couleurs personnalisées -->
                            <div class="border-t pt-4">
                                <p class="text-xs text-gray-600 mb-3">Ajouter une couleur personnalisée :</p>
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center space-x-2">
                                        <input type="color" id="customColorPicker" value="#ff0000"
                                               class="w-16 h-12 border-2 border-gray-300 rounded-lg cursor-pointer shadow-sm">
                                        <!-- Prévisualisation de la couleur sélectionnée -->
                                        <div id="colorPreview" class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm" style="background-color: #ff0000;"></div>
                                    </div>
                                    <input type="text" id="customColorName" placeholder="Nom de la couleur"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" onclick="addCustomColor()"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Ajouter
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <label class="flex items-center space-x-2 text-xs text-gray-600">
                                        <input type="checkbox" id="forceCustomName" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                        <span>Forcer l'utilisation du nom personnalisé (ignorer l'auto-remplissage)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Couleurs personnalisées ajoutées -->
                            <div id="customColorsContainer" class="space-y-2">
                                @foreach($customColors as $customColor)
                                    <div class="custom-color-item flex items-center space-x-2 p-2 bg-gray-50 rounded-lg">
                                        <span class="w-4 h-4 rounded-full border border-gray-300"
                                              style="background-color: {{ $customColor['hex'] ?: \App\Helpers\ColorHelper::generateColorFromName($customColor['name']) }}"></span>
                                        <span class="text-sm text-gray-700">{{ $customColor['name'] }}</span>
                                        <button type="button" onclick="removeCustomColor(this)"
                                                class="text-red-600 hover:text-red-800 ml-auto">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="hidden" name="couleurs_personnalisees[]" value="{{ $customColor['name'] }}">
                                    </div>
                                @endforeach
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

                    <!-- Quantité en Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en Stock *</label>
                        <input type="number" name="quantite_stock" value="{{ old('quantite_stock', $product->quantite_stock) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
    colorItem.className = 'custom-color-item flex items-center space-x-2 p-2 bg-gray-50 rounded-lg';
    colorItem.innerHTML = `
        <span class="w-4 h-4 rounded-full border border-gray-300" style="background-color: ${colorHex}"></span>
        <span class="text-sm text-gray-700">${colorName}</span>
        <button type="button" onclick="removeCustomColor(this)" class="text-red-600 hover:text-red-800 ml-auto">
            <i class="fas fa-times"></i>
        </button>
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

    customColorCounter++;
}

// Supprimer une couleur personnalisée
function removeCustomColor(button) {
    button.closest('.custom-color-item').remove();
    // Mettre à jour le champ couleurs combinées
    updateCombinedColors();
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
});

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
</script>
@endsection


