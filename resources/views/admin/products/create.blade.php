@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Ajouter un Produit</h1>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du produit -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Produit *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
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
                                <option value="{{ $category->id }}" @selected(old('categorie_id') == $category->id)>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Couleurs du Produit *</label>
                        <p class="text-xs text-gray-500 mb-3">Sélectionnez des couleurs disponibles ou ajoutez vos propres couleurs personnalisées.</p>

                        <!-- Couleurs prédéfinies avec checkboxes -->
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-3">Couleurs disponibles :</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @php
                                    $predefinedColors = [
                                        'Rouge' => '#ff0000', 'Vert' => '#00ff00', 'Bleu' => '#0000ff', 'Jaune' => '#ffff00',
                                        'Orange' => '#ffa500', 'Violet' => '#800080', 'Rose' => '#ffc0cb', 'Marron' => '#a52a2a',
                                        'Noir' => '#000000', 'Blanc' => '#ffffff', 'Gris' => '#808080', 'Beige' => '#f5f5dc',
                                        'Turquoise' => '#40e0d0', 'Or' => '#ffd700', 'Argent' => '#c0c0c0', 'Bordeaux' => '#800020'
                                    ];
                                @endphp
                                @foreach($predefinedColors as $name => $hex)
                                    <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 border border-gray-200">
                                        <label class="flex items-center space-x-3 cursor-pointer flex-1">
                                            <input type="checkbox" name="couleurs[]" value="{{ $name }}"
                                                   @checked(in_array($name, old('couleurs', [])))
                                                   data-hex="{{ $hex }}"
                                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                   onchange="updateColorHex(this)">
                                            <div class="w-6 h-6 rounded-full border-2 border-gray-300 shadow-sm" style="background-color: {{ $hex }}"></div>
                                            <span class="text-sm text-gray-700">{{ $name }}</span>
                                        </label>
                                        <div class="flex items-center space-x-2">
                                            <label class="text-xs text-gray-600">Stock:</label>
                                            <input type="number" name="stock_couleur_{{ $loop->index }}"
                                                   placeholder="0" min="0"
                                                   class="w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500"
                                                   value="{{ old('stock_couleur_' . $loop->index, 0) }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                                                <!-- Interface d'ajout de couleur personnalisée -->
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm font-medium text-blue-800 mb-3">Ajouter une couleur personnalisée :</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex items-center space-x-2">
                                    <input type="color" id="newColorPicker" value="#ff6b6b" class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                                    <!-- Prévisualisation de la couleur sélectionnée -->
                                    <div id="colorPreview" class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm" style="background-color: #ff6b6b;"></div>
                                    <span class="text-sm text-gray-600">Couleur</span>
                                </div>
                                <div class="flex-1">
                                    <input type="text" id="newColorName" placeholder="Nom de la couleur (ex: Corail, Indigo)"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <button type="button" onclick="addCustomColor()"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Ajouter
                                </button>
                            </div>
                        </div>

                        <!-- Container des couleurs personnalisées ajoutées -->
                        <div id="customColorsContainer" class="mb-4">
                            <!-- Les couleurs personnalisées seront ajoutées ici dynamiquement -->
                        </div>

                        <!-- Inputs cachés pour les couleurs personnalisées -->
                        <div id="hiddenCustomColorsInputs">
                            <!-- Les inputs cachés seront ajoutés ici dynamiquement -->
                        </div>

                        @error('couleurs')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tailles -->
                    <div class="md:col-span-2" id="taillesSection">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tailles Disponibles <span id="taillesRequired" class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-500 mb-2">Cochez des tailles standards ou ajoutez des tailles personnalisées (ex: 37, 32 Bébé, ESPA 37).</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                            @php
                                $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'];
                            @endphp
                            @foreach($sizes as $size)
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="tailles[]" value="{{ $size }}"
                                           @checked(in_array($size, old('tailles', [])))
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 taille-checkbox">
                                    <span class="text-sm text-gray-700">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="text" id="customSizeInput" placeholder="Ex: ESPA 37, 32 Bébé" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" onclick="addCustomSize()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Ajouter</button>
                        </div>
                        <div id="customSizesContainer" class="flex flex-wrap gap-2 mt-3"></div>
                        <input type="hidden" id="customSizesHidden" name="tailles[]">
                        @error('tailles')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix Admin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix Admin (MAD) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">MAD</span>
                            <input type="number" name="prix_admin" value="{{ old('prix_admin') }}" step="0.01" min="0" required
                                   placeholder="0.00" class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                            <input type="number" name="prix_vente" value="{{ old('prix_vente') }}" step="0.01" min="0" required
                                   placeholder="0.00" class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('prix_vente')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantité en Stock -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité en Stock *</label>
                        <input type="number" name="quantite_stock" value="{{ old('quantite_stock', 0) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('quantite_stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image du Produit</label>
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
                        <i class="fas fa-save mr-2"></i>Créer le Produit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Système de couleurs SIMPLE : checkboxes seulement
console.log('Système de couleurs simplifié chargé');

// Compteur pour les couleurs personnalisées
let customColorCounter = 0;

// Fonction pour les tailles personnalisées
function addCustomSize() {
    const input = document.getElementById('customSizeInput');
    const container = document.getElementById('customSizesContainer');

    if (!input || !container) {
        console.error('Éléments des tailles non trouvés');
        return;
    }

    let value = input.value ? String(input.value).trim() : '';
    if (!value) return;

    // Vérifier si la taille existe déjà
    const exists = Array.from(document.querySelectorAll('input[name="tailles[]"]'))
        .some(el => el.value.toLowerCase() === value.toLowerCase());
    if (exists) {
        input.value = '';
        return;
    }

    // Créer l'élément visuel
    const sizeElement = document.createElement('div');
    sizeElement.className = 'flex items-center space-x-2 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 mb-2';
    sizeElement.innerHTML = `
        <span class="text-sm font-medium text-blue-800">${value}</span>
        <button type="button" onclick="this.parentElement.remove()"
                class="text-red-500 hover:text-red-700 px-2 py-1">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(sizeElement);

    // Ajouter à l'input caché
    const hiddenInput = document.getElementById('customSizesHidden');
    if (hiddenInput) {
        const newInput = document.createElement('input');
        newInput.type = 'hidden';
        newInput.name = 'tailles[]';
        newInput.value = value;
        hiddenInput.appendChild(newInput);
    }

    // Réinitialiser l'input
    input.value = '';
}

// Fonction pour supprimer une couleur personnalisée
function removeCustomColor(button) {
    button.parentElement.remove();
}

// Fonction pour les couleurs personnalisées
function addCustomColor() {
    const colorPicker = document.getElementById('newColorPicker');
    const colorNameInput = document.getElementById('newColorName');
    const customColorsContainer = document.getElementById('customColorsContainer');
    const colorPreview = document.getElementById('colorPreview');

    if (!colorPicker || !colorNameInput || !customColorsContainer || !colorPreview) {
        console.error('Éléments des couleurs personnalisées non trouvés');
        return;
    }

    const colorName = colorNameInput.value ? String(colorNameInput.value).trim() : '';
    const colorHex = colorPicker.value;

    if (!colorName || !colorHex) {
        alert('Veuillez sélectionner une couleur et entrer un nom.');
        return;
    }

    // Vérifier si la couleur existe déjà
    const exists = Array.from(document.querySelectorAll('input[name="couleurs[]"]'))
        .some(el => el.value.toLowerCase() === colorName.toLowerCase());
    if (exists) {
        alert('Cette couleur personnalisée existe déjà.');
        return;
    }

    // Créer l'élément visuel avec l'input caché intégré
    const colorElement = document.createElement('div');
    colorElement.className = 'flex items-center space-x-3 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 mb-2';
    colorElement.innerHTML = `
        <input type="hidden" name="couleurs[]" value="${colorName}">
        <input type="hidden" name="couleurs_hex[]" value="${colorHex}">
        <div class="w-6 h-6 rounded-full border-2 border-gray-300 shadow-sm" style="background-color: ${colorHex}"></div>
        <span class="text-sm font-medium text-blue-800">${colorName}</span>
        <div class="flex items-center space-x-2 ml-auto">
            <label class="text-xs text-gray-600">Stock:</label>
            <input type="number" name="stock_couleur_custom_${customColorCounter}"
                   placeholder="0" min="0"
                   class="w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-1 focus:ring-blue-500"
                   value="0">
            <button type="button" onclick="removeCustomColor(this)"
                    class="text-red-500 hover:text-red-700 px-2 py-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    customColorsContainer.appendChild(colorElement);

    // Incrémenter le compteur pour la prochaine couleur personnalisée
    customColorCounter++;

    // Réinitialiser les inputs
    colorPicker.value = '#ff6b6b'; // Reset color picker to default color
    colorNameInput.value = '';
    colorPreview.style.backgroundColor = '#ff6b6b'; // Reset preview
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
}

// Fonction pour mettre à jour la prévisualisation en temps réel
function updateColorPreview() {
    const colorPicker = document.getElementById('newColorPicker');
    const colorPreview = document.getElementById('colorPreview');

    if (!colorPicker || !colorPreview) return;

    const selectedColor = colorPicker.value;
    colorPreview.style.backgroundColor = selectedColor;
    console.log('Couleur sélectionnée:', selectedColor);
}

// Initialisation simple
document.addEventListener('DOMContentLoaded', function() {
    console.log('Formulaire initialisé avec système de couleurs personnalisées');

    // Initialiser la prévisualisation avec la couleur par défaut
    const colorPicker = document.getElementById('newColorPicker');
    const colorPreview = document.getElementById('colorPreview');

    if (colorPicker && colorPreview) {
        colorPreview.style.backgroundColor = colorPicker.value;

        // Ajouter les événements pour le color picker
        colorPicker.addEventListener('change', updateColorPreview);
        colorPicker.addEventListener('input', updateColorPreview);
    }

    // Validation simple du formulaire
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Tentative de soumission du formulaire');

            // Vérifier qu'il y a au moins une couleur sélectionnée (prédéfinie ou personnalisée)
            const selectedPredefinedColors = Array.from(document.querySelectorAll('input[name="couleurs[]"]:checked'));
            const customColors = Array.from(document.querySelectorAll('#customColorsContainer input[name="couleurs[]"]'));

            const totalColors = selectedPredefinedColors.length + customColors.length;
            console.log('Couleurs prédéfinies sélectionnées:', selectedPredefinedColors.length);
            console.log('Couleurs personnalisées ajoutées:', customColors.length);
            console.log('Total des couleurs:', totalColors);

            if (totalColors === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins une couleur (prédéfinie ou personnalisée)');
                console.log('Soumission bloquée - aucune couleur sélectionnée');
                return;
            }

            console.log('Validation OK - soumission autorisée');
        });
    } else {
        console.error('Formulaire non trouvé');
    }

    // Gestion conditionnelle des tailles selon la catégorie
    const categorieSelect = document.getElementById('categorie_id');
    const taillesSection = document.getElementById('taillesSection');
    const taillesRequired = document.getElementById('taillesRequired');
    const tailleCheckboxes = document.querySelectorAll('.taille-checkbox');
    const customSizeInput = document.getElementById('customSizeInput');
    const addSizeButton = document.querySelector('button[onclick="addCustomSize()"]');

    console.log('Elements trouvés (create):');
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

                // Vider les tailles personnalisées
                document.getElementById('customSizesContainer').innerHTML = '';
                document.getElementById('customSizeInput').value = '';

                // Désactiver les inputs
                customSizeInput.disabled = true;
                addSizeButton.disabled = true;
            } else {
                // Afficher la section des tailles pour les autres catégories
                taillesSection.style.display = 'block';
                taillesRequired.style.display = 'inline';

                // Réactiver les inputs et remettre l'attribut name
                tailleCheckboxes.forEach(checkbox => {
                    checkbox.setAttribute('name', 'tailles[]');
                    checkbox.disabled = false;
                });
                customSizeInput.disabled = false;
                addSizeButton.disabled = false;
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
</script>
@endsection


