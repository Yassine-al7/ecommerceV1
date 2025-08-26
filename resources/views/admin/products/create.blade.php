@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">إضافة منتج</h1>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>رجوع
                </a>
            </div>

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du produit -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المنتج *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">التصنيف *</label>
                        <select name="categorie_id" id="categorie_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">اختر تصنيفًا</option>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">ألوان المنتج *</label>
                        <p class="text-xs text-gray-500 mb-3">اختر الألوان المتاحة أو أضف ألوانًا مخصصة.</p>

                        <!-- Couleurs prédéfinies avec checkboxes -->
                        <div class="mb-6">
                            <p class="text-sm font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-palette mr-2 text-blue-600"></i>
                                الألوان المتاحة: <span id="selectedColorsCount" class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">0 محددة</span>
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @php
                                    $predefinedColors = [
                                        'Rouge' => '#ff0000', 'Vert' => '#00ff00', 'Bleu' => '#0000ff', 'Jaune' => '#ffff00',
                                        'Orange' => '#ffa500', 'Violet' => '#800080', 'Rose' => '#ffc0cb', 'Marron' => '#a52a2a',
                                        'Noir' => '#000000', 'Blanc' => '#ffffff', 'Gris' => '#808080', 'Beige' => '#f5f5dc',
                                        'Turquoise' => '#40e0d0', 'Or' => '#ffd700', 'Argent' => '#c0c0c0', 'Bordeaux' => '#800020'
                                    ];
                                @endphp
                                @foreach($predefinedColors as $name => $hex)
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300 color-card" data-color-name="{{ $name }}">
                                        <div class="flex items-center justify-between mb-3">
                                            <label class="flex items-center space-x-3 cursor-pointer flex-1">
                                                <input type="checkbox" name="couleurs[]" value="{{ $name }}"
                                                       @checked(in_array($name, old('couleurs', [])))
                                                       data-hex="{{ $hex }}"
                                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 color-checkbox"
                                                       onchange="updateColorHex(this)">
                                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm color-preview" style="background-color: {{ $hex }}"></div>
                                                <span class="text-sm font-medium text-gray-700 color-name">{{ $name }}</span>
                                            </label>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <label class="text-xs font-medium text-gray-600">المخزون:</label>
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
                                إضافة لون مخصص
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                <div class="flex flex-col space-y-2">
                                    <label class="text-xs font-medium text-blue-700">اختر اللون</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="color" id="newColorPicker" value="#ff6b6b"
                                               class="w-14 h-12 border-2 border-gray-300 rounded-lg cursor-pointer shadow-sm">
                                        <div id="colorPreview" class="w-10 h-10 rounded-full border-2 border-gray-300 shadow-sm"
                                             style="background-color: #ff6b6b;"></div>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="text-xs font-medium text-blue-700 mb-2 block">اسم اللون</label>
                                    <input type="text" id="newColorName" placeholder="مثال: مرجاني، نيلي، بحري..."
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="addCustomColor()"
                                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 hover:shadow-md font-medium">
                                        <i class="fas fa-plus mr-2"></i>إضافة
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Résumé des couleurs sélectionnées -->
                        <div id="selectedColorsSummary" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg" style="display: none;">
                            <h4 class="text-sm font-semibold text-green-800 mb-2 flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                الألوان المحددة:
                            </h4>
                            <div id="selectedColorsList" class="flex flex-wrap gap-2">
                                <!-- Les couleurs sélectionnées seront affichées ici -->
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">المقاسات المتاحة <span id="taillesRequired" class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-500 mb-2">حدد مقاسات قياسية أو أضف مقاسات مخصصة.</p>
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
                            <input type="text" id="customSizeInput" placeholder="مثال: ESPA 37" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" onclick="addCustomSize()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">إضافة</button>
                        </div>
                        <div id="customSizesContainer" class="flex flex-wrap gap-2 mt-3"></div>
                        <input type="hidden" id="customSizesHidden" name="tailles[]">
                        @error('tailles')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix Admin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">سعر المشرف (MAD) *</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">سعر البيع (MAD) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">MAD</span>
                            <input type="number" name="prix_vente" value="{{ old('prix_vente') }}" step="0.01" min="0" required
                                   placeholder="0.00" class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('prix_vente')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantité en Stock Total (Calculée automatiquement) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الكمية الإجمالية في المخزون</label>
                        <input type="number" id="stockTotal" value="0" min="0" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-semibold">
                        <p class="text-xs text-gray-500 mt-1">💡 يُحسب تلقائيًا: مجموع مخزون جميع الألوان</p>
                        <input type="hidden" name="quantite_stock" id="stockTotalHidden" value="0">
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">صورة المنتج</label>
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
                        <i class="fas fa-save mr-2"></i>إنشاء المنتج
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
    colorElement.className = 'bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300 mb-3';
    colorElement.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-3">
                <input type="hidden" name="couleurs[]" value="${colorName}">
                <input type="hidden" name="couleurs_hex[]" value="${colorHex}">
                <div class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm" style="background-color: ${colorHex}"></div>
                <span class="text-sm font-medium text-gray-700">${colorName}</span>
            </div>
            <button type="button" onclick="removeCustomColor(this)"
                    class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-full transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex items-center space-x-2">
            <label class="text-xs font-medium text-gray-600">المخزون:</label>
            <input type="number" name="stock_couleur_custom_${customColorCounter}"
                   placeholder="0" min="0"
                   class="W-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                   value="0">
        </div>
    `;

    customColorsContainer.appendChild(colorElement);

    // Incrémenter le compteur pour la prochaine couleur personnalisée
    customColorCounter++;

    // Ajouter l'écouteur d'événements pour le stock
    const stockInput = colorElement.querySelector('input[name^="stock_couleur_custom_"]');
    if (stockInput) {
        stockInput.addEventListener('input', calculateTotalStock);
    }

    // Recalculer le stock total
    calculateTotalStock();

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

        // Mettre à jour l'affichage des couleurs sélectionnées
        updateSelectedColorsCount();
        updateSelectedColorsSummary();

        // Recalculer le stock total
        calculateTotalStock();
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

        // Fonction pour calculer le stock total
    function calculateTotalStock() {
        let total = 0;

        // Calculer le stock des couleurs prédéfinies
        const predefinedStockInputs = document.querySelectorAll('input[name^="stock_couleur_"]');

                        predefinedStockInputs.forEach((input, index) => {
            // Trouver la checkbox de couleur en remontant jusqu'au conteneur principal
            let colorContainer = input.parentElement;
            let checkbox = null;

            // Remonter jusqu'à trouver le conteneur avec la checkbox
            while (colorContainer && !checkbox) {
                checkbox = colorContainer.querySelector('input[name="couleurs[]"]');
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
            // Forcer la mise à jour de l'affichage avec plusieurs méthodes
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

        // Ajouter les écouteurs d'événements pour les inputs de stock des couleurs prédéfinies
        const predefinedStockInputs = document.querySelectorAll('input[name^="stock_couleur_"]');
        predefinedStockInputs.forEach(input => {
            input.addEventListener('input', calculateTotalStock);
        });

        // Ajouter les écouteurs d'événements pour les checkboxes des couleurs prédéfinies
        const predefinedColorCheckboxes = document.querySelectorAll('input[name="couleurs[]"]');
        predefinedColorCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', calculateTotalStock);
        });

        // Calculer le stock total initial
        calculateTotalStock();

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

    // Fonction pour mettre à jour le compteur de couleurs sélectionnées
    function updateSelectedColorsCount() {
        const selectedColors = document.querySelectorAll('input[name="couleurs[]"]:checked');
        const countElement = document.getElementById('selectedColorsCount');
        if (countElement) {
            countElement.textContent = `${selectedColors.length} sélectionnée${selectedColors.length > 1 ? 's' : ''}`;
        }
    }

    // Fonction pour afficher le résumé des couleurs sélectionnées
    function updateSelectedColorsSummary() {
        const selectedColors = document.querySelectorAll('input[name="couleurs[]"]:checked');
        const summaryElement = document.getElementById('selectedColorsSummary');
        const listElement = document.getElementById('selectedColorsList');

        if (selectedColors.length === 0) {
            summaryElement.style.display = 'none';
            return;
        }

        summaryElement.style.display = 'block';
        listElement.innerHTML = '';

        selectedColors.forEach(checkbox => {
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
    }

    // Initialiser l'affichage au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        updateSelectedColorsCount();
        updateSelectedColorsSummary();
    });
});
</script>
@endsection


