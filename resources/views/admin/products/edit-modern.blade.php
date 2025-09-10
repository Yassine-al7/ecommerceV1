@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-edit text-blue-600 mr-3"></i>
                    تعديل المنتج: {{ $product->name }}
                </h1>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>رجوع
                </a>
            </div>

            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-8" id="productForm">
                @csrf
                @method('PUT')

                <!-- Informations de base -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                    <h2 class="text-xl font-semibold text-blue-800 mb-6 flex items-center">
                        <i class="fas fa-info-circle mr-3"></i>
                        المعلومات الأساسية
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom du produit -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">اسم المنتج *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   placeholder="أدخل اسم المنتج">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catégorie -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">التصنيف *</label>
                            <select name="categorie_id" id="categorie_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">اختر تصنيفًا</option>
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

                        <!-- Image principale -->
                        <div>
                            <label for="mainImageInput" class="block text-sm font-medium text-gray-700 mb-2">صورة المنتج الرئيسية</label>
                            <div class="relative">
                                <input type="file" name="image" accept="image/*" id="mainImageInput"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       onchange="previewMainImage(this)">
                                <div class="mt-2 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    يمكنك رفع عدة صور (JPG, PNG, GIF) - الحد الأقصى 5MB لكل صورة
                                </div>
                                <!-- Prévisualisation de l'image principale -->
                                <div id="mainImagePreviewContainer" class="mt-3 {{ $product->image ? '' : 'hidden' }}">
                                    @php
                                        $src = trim($product->image ?? '', '/');
                                        if (preg_match('#^https?://#i', $src)) {
                                            $imageUrl = $src;
                                        } elseif ($src) {
                                            $imageUrl = $product->image;
                                        } else {
                                            $imageUrl = '';
                                        }
                                    @endphp
                                    <img id="mainImagePreview" class="w-32 h-32 object-cover rounded-lg border border-gray-300"
                                         src="{{ $imageUrl }}" alt="Prévisualisation">
                                </div>
                            </div>
                            @error('image')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Gestion des couleurs et images -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                    <h2 class="text-xl font-semibold text-purple-800 mb-6 flex items-center">
                        <i class="fas fa-palette mr-3"></i>
                        الألوان والصور
                    </h2>

                    <!-- Interface moderne de sélection des couleurs -->
                    <div class="space-y-6">
                        <!-- Couleurs prédéfinies -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-swatchbook mr-2 text-purple-600"></i>
                                الألوان المتاحة
                                <span id="selectedColorsCount" class="ml-3 px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full">0 محددة</span>
                            </h3>

                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 items-start">
                                @php
                                    $predefinedColors = [
                                        'Rouge' => '#ef4444', 'Vert' => '#22c55e', 'Bleu' => '#3b82f6', 'Jaune' => '#eab308',
                                        'Orange' => '#f97316', 'Violet' => '#8b5cf6', 'Rose' => '#ec4899', 'Marron' => '#a3a3a3',
                                        'Noir' => '#000000', 'Blanc' => '#ffffff', 'Gris' => '#6b7280', 'Beige' => '#d4af37',
                                        'Turquoise' => '#06b6d4', 'Or' => '#fbbf24', 'Argent' => '#9ca3af', 'Bordeaux' => '#7c2d12'
                                    ];

                                    // Récupérer les couleurs existantes du produit
                                    $existingColors = $product->couleur ?? [];
                                    $existingColorImages = $product->color_images ?? [];

                                    $activeColors = [];
                                    $imagesByColor = [];

                                    // Traiter les couleurs existantes (nouveau système simplifié)
                                    if (is_array($existingColors)) {
                                        foreach ($existingColors as $color) {
                                            if (is_array($color) && isset($color['name'])) {
                                                $activeColors[] = $color['name'];
                                            }
                                        }
                                    }

                                    // Traiter les images par couleur
                                    if (is_array($existingColorImages)) {
                                        foreach ($existingColorImages as $colorImage) {
                                            if (is_array($colorImage) && isset($colorImage['color'])) {
                                                $imagesByColor[$colorImage['color']] = $colorImage['images'] ?? [];
                                            }
                                        }
                                    }
                                @endphp
                                @php
                                    // Préparer un mapping du stock existant par couleur
                                    $stockByColor = [];
                                    $rawStock = $product->stock_couleurs ?? [];
                                    if (is_string($rawStock)) {
                                        $rawStock = json_decode($rawStock, true) ?: [];
                                    }
                                    if (is_array($rawStock)) {
                                        foreach ($rawStock as $sc) {
                                            if (is_array($sc) && isset($sc['name'])) {
                                                $stockByColor[$sc['name']] = (int)($sc['quantity'] ?? 0);
                                            }
                                        }
                                    }
                                @endphp
                                @php
                                    $colorIndex = 0; // Index pour les champs de stock
                                @endphp
                                @foreach($predefinedColors as $name => $hex)
                                    @php
                                        $isActive = in_array($name, $activeColors);
                                        $existingImages = $imagesByColor[$name] ?? [];
                                        $currentIndex = $isActive ? $colorIndex : null; // Index seulement pour les couleurs actives
                                        if ($isActive) $colorIndex++; // Incrémenter seulement pour les couleurs actives
                                    @endphp
                                    <div class="color-card bg-white border-2 border-gray-200 rounded-xl p-4 hover:shadow-lg transition-all duration-300 cursor-pointer group {{ $isActive ? 'selected' : '' }} w-full"
                                         data-color-name="{{ $name }}" data-color-hex="{{ $hex }}">
                                        <div class="flex flex-col items-center space-y-3 w-full">
                                            <!-- Checkbox et couleur -->
                                            <div class="flex items-center space-x-3 w-full justify-center">
                                                <input type="checkbox" name="couleurs[]" value="{{ $name }}"
                                                       @checked($isActive)
                                                       data-hex="{{ $hex }}"
                                                       class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 color-checkbox"
                                                       onchange="toggleColorCard(this)">
                                                <input type="hidden" name="couleurs_hex[]" value="{{ $hex }}" class="color-hex-input">
                                                <div class="w-12 h-12 rounded-full border-3 border-gray-300 shadow-md color-preview group-hover:scale-110 transition-transform duration-200"
                                                     style="background-color: {{ $hex }}"></div>
                                            </div>

                                            <!-- Nom de la couleur -->
                                            <span class="text-sm font-medium text-gray-700 text-center color-name">{{ $name }}</span>

                                            <!-- Champ de stock pour cette couleur (aligné sur le create) -->
                                            <div class="w-full stock-field" style="display: {{ $isActive ? 'block' : 'none' }};">
                                                <label class="block text-xs text-gray-600 mb-1">المخزون</label>
                                                <input type="number"
                                                       name="stock_couleur_{{ $isActive ? $currentIndex : $loop->index }}"
                                                       value="{{ old('stock_couleur_' . ($isActive ? $currentIndex : $loop->index), $stockByColor[$name] ?? 0) }}"
                                                       min="0"
                                                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 stock-input"
                                                       placeholder="0"
                                                       oninput="calculateTotalStock()">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @error('couleurs')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gestion des couleurs masquées -->
                <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl p-6 border border-red-200">
                    <h2 class="text-xl font-semibold text-red-800 mb-6 flex items-center">
                        <i class="fas fa-eye-slash mr-3"></i>
                        إدارة الألوان المخفية
                    </h2>

                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>
                            يمكنك إخفاء الألوان المنفذة من المخزون حتى لا يتمكن البائعون من اختيارها في الطلبات
                        </p>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                            @php
                                // S'assurer que hidden_colors est un tableau
                                $hiddenColors = $product->hidden_colors ?? [];
                                if (is_string($hiddenColors)) {
                                    $hiddenColors = json_decode($hiddenColors, true) ?? [];
                                }

                                // S'assurer que activeColors est un tableau
                                $activeColorsArray = $activeColors ?? [];
                                if (is_string($activeColorsArray)) {
                                    $activeColorsArray = json_decode($activeColorsArray, true) ?? [];
                                }
                            @endphp
                            @foreach($predefinedColors as $name => $hex)
                                @php
                                    $isHidden = in_array($name, $hiddenColors);
                                    $isActive = in_array($name, $activeColorsArray);
                                @endphp
                                @if($isActive)
                                    <div class="relative">
                                        <label class="flex flex-col items-center space-y-2 cursor-pointer p-3 rounded-lg border-2 transition-all duration-200 {{ $isHidden ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                            <input type="checkbox" name="hidden_colors[]" value="{{ $name }}"
                                                   @checked($isHidden)
                                                   class="hidden">
                                            <div class="w-10 h-10 rounded-full border-2 border-gray-300 shadow-sm {{ $isHidden ? 'opacity-50' : '' }}"
                                                 style="background-color: {{ $hex }}"></div>
                                            <span class="text-xs font-medium text-center {{ $isHidden ? 'text-red-600' : 'text-gray-700' }}">
                                                {{ $name }}
                                            </span>
                                            @if($isHidden)
                                                <i class="fas fa-eye-slash text-red-500 text-xs"></i>
                                            @endif
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>ملاحظة:</strong> الألوان المخفية لن تظهر في قوائم المنتجات ولن يتمكن البائعون من اختيارها في الطلبات
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tailles -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200" id="taillesSection">
                    <h2 class="text-xl font-semibold text-green-800 mb-6 flex items-center">
                        <i class="fas fa-ruler mr-3"></i>
                        المقاسات المتاحة <span id="taillesRequired" class="text-red-500">*</span>
                    </h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
                        @php
                            $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'];
                            $currentSizes = is_string($product->tailles) ? (json_decode($product->tailles, true) ?? []) : (is_array($product->tailles) ? $product->tailles : []);
                        @endphp
                        @foreach($sizes as $size)
                            <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-green-100 transition-colors">
                                <input type="checkbox" name="tailles[]" value="{{ $size }}"
                                       @checked(in_array($size, old('tailles', $currentSizes)))
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 taille-checkbox">
                                <span class="text-sm text-gray-700">{{ $size }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex items-center space-x-2">
                        <input type="text" id="customSizeInput" placeholder="مثال: ESPA 37"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <button type="button" onclick="addCustomSize()"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            إضافة
                        </button>
                    </div>
                    <div id="customSizesContainer" class="flex flex-wrap gap-2 mt-3"></div>
                    <input type="hidden" id="customSizesHidden" name="tailles[]">

                    @error('tailles')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Global (calculé depuis stock par couleur) -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                    <h2 class="text-xl font-semibold text-blue-800 mb-6 flex items-center">
                        <i class="fas fa-boxes mr-3"></i>
                        المخزون الإجمالي
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                المخزون الإجمالي (محسوب)
                            </label>
                            <input type="number" id="stockTotal" value="0" readonly
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-lg font-medium">
                            <input type="hidden" id="stockTotalHidden" name="quantite_stock" value="0">
                            <p class="text-sm text-gray-500 mt-2">
                                💡 يتم حسابه تلقائياً من مجموع مخزون الألوان.
                            </p>
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-4xl mb-2">📦</div>
                                <p class="text-sm text-gray-600">
                                    <strong>إدارة المخزون:</strong><br>
                                    • أدخل العدد الإجمالي<br>
                                    • أضف صور لكل لون<br>
                                    • أخف الألوان المنفذة لاحقاً
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prix -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-200">
                    <h2 class="text-xl font-semibold text-yellow-800 mb-6 flex items-center">
                        <i class="fas fa-dollar-sign mr-3"></i>
                        الأسعار
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Prix Admin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تمن المقترح للبيع (MAD) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500">MAD</span>
                                <input type="text" name="prix_admin" value="{{ old('prix_admin', implode(',', $product->prix_admin_array)) }}" required
                                       placeholder="مثال: 150-200 أو 150,200,250" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">يمكنك إدخال سعر واحد أو عدة أسعار مفصولة بفاصلة</p>
                            @error('prix_admin')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prix de Vente -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تمن (MAD) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500">MAD</span>
                                <input type="number" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente) }}" step="0.01" min="0" required
                                       placeholder="0.00" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                            @error('prix_vente')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" onclick="resetForm()"
                            class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-undo mr-2"></i>إعادة تعيين
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition-all duration-200 hover:shadow-lg font-medium">
                        <i class="fas fa-save mr-2"></i>تحديث المنتج
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Styles personnalisés -->
<style>
.color-card {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.color-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.color-card.selected {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
}

.color-card.selected::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #8b5cf6, #ec4899);
}

.color-preview {
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.color-preview:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.color-details {
    animation: slideDown 0.3s ease-out;
}

/* Harmonise edit with create: hide in-card details; images managed in separate section */
.color-card .color-details {
    display: none !important;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.image-preview {
    display: inline-block;
    margin: 2px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.image-preview img {
    width: 40px;
    height: 40px;
    object-fit: cover;
}

/* Animations pour les transitions */
.transition-all {
    transition: all 0.3s ease;
}

/* Styles pour les inputs focus */
input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

<script>
// Variables globales
let customColorCounter = {{ count($customColors ?? []) }};

// Initialiser les champs cachés des codes hex
document.addEventListener('DOMContentLoaded', function() {
    // Désactiver tous les champs hex par défaut
    document.querySelectorAll('.color-hex-input').forEach(input => {
        input.disabled = true;
    });

    // Activer les champs hex pour les couleurs déjà sélectionnées
    document.querySelectorAll('input[name="couleurs[]"]:checked').forEach(checkbox => {
        const colorCard = checkbox.closest('.color-card');
        const hexInput = colorCard ? colorCard.querySelector('.color-hex-input') : null;
        if (hexInput) hexInput.disabled = false;
    });
});
let removedImages = [];

// Fonction pour basculer l'affichage des détails d'une couleur
function toggleColorCard(checkbox) {
    const colorCard = checkbox.closest('.color-card');
    const hexInput = colorCard.querySelector('.color-hex-input');

    if (checkbox.checked) {
        colorCard.classList.add('selected');
        if (hexInput) hexInput.disabled = false;

        // Changer l'image principale du produit
        changeMainProductImage(colorCard);
    } else {
        colorCard.classList.remove('selected');
        if (hexInput) hexInput.disabled = true;
    }

    updateSelectedColorsCount();
}

// Fonction pour prévisualiser l'image principale
function previewMainImage(input) {
    const previewContainer = document.getElementById('mainImagePreviewContainer');
    const preview = document.getElementById('mainImagePreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Fonction pour changer l'image principale du produit selon la couleur
function changeMainProductImage(colorCard) {
    const colorName = colorCard.querySelector('.color-name').textContent;
    const colorPreview = colorCard.querySelector('.color-preview');
    const backgroundColor = colorPreview.style.backgroundColor;

    // Trouver l'image principale du produit
    const mainImagePreview = document.getElementById('mainImagePreview');
    const mainImagePreviewContainer = document.getElementById('mainImagePreviewContainer');

    if (mainImagePreview && mainImagePreviewContainer) {
        // Chercher s'il y a une image existante pour cette couleur
        const existingImages = existingColorImages[colorName] || [];

        if (existingImages && existingImages.length > 0) {
            // Utiliser la première image existante de cette couleur
            const firstImage = existingImages[0];
            mainImagePreview.src = firstImage;
            mainImagePreview.alt = `Image ${colorName}`;
            mainImagePreviewContainer.classList.remove('hidden');

            // Afficher un message avec le nom de la couleur
            const imageLabel = document.querySelector('label[for="mainImageInput"]');
            if (imageLabel) {
                imageLabel.innerHTML = `صورة المنتج الرئيسية <span class="text-sm text-gray-500">(${colorName})</span>`;
            }
        } else {
            // Créer une image temporaire avec la couleur si aucune image n'existe
            const canvas = document.createElement('canvas');
            canvas.width = 200;
            canvas.height = 200;
            const ctx = canvas.getContext('2d');

            // Remplir avec la couleur de la carte
            ctx.fillStyle = backgroundColor;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Ajouter le nom de la couleur
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 20px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(colorName, canvas.width/2, canvas.height/2);

            // Ajouter une bordure
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 4;
            ctx.strokeRect(0, 0, canvas.width, canvas.height);

            // Convertir en image
            const dataURL = canvas.toDataURL();
            mainImagePreview.src = dataURL;
            mainImagePreview.alt = `Image ${colorName}`;
            mainImagePreviewContainer.classList.remove('hidden');

            // Afficher un message
            const imageLabel = document.querySelector('label[for="mainImageInput"]');
            if (imageLabel) {
                imageLabel.innerHTML = `صورة المنتج الرئيسية <span class="text-sm text-gray-500">(${colorName})</span>`;
            }
        }
    }
}

// Fonction pour récupérer les images existantes d'une couleur
function getExistingImagesForColor(colorName) {
    // Chercher dans les images existantes affichées dans la section des couleurs
    const colorCard = document.querySelector(`[data-color-name="${colorName}"]`);
    if (!colorCard) return [];

    const existingImagesContainer = colorCard.querySelector('.existing-images');
    if (!existingImagesContainer) return [];

    const images = existingImagesContainer.querySelectorAll('img');
    const imageUrls = [];

    images.forEach(img => {
        if (img.src && !img.src.includes('data:')) {
            imageUrls.push(img.src);
        }
    });

    return imageUrls;
}

// Données des images existantes (passées depuis PHP)
const existingColorImages = @json($imagesByColor ?? []);



// Fonction pour prévisualiser les images d'une couleur
function previewColorImages(input, colorName) {
    const previewContainer = document.getElementById(`preview-${colorName}`);
    if (!previewContainer) return;

    previewContainer.innerHTML = '';

    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-16 h-16 object-cover rounded-lg border border-gray-300 mr-2 mb-2';
                    img.alt = `Image ${index + 1}`;
                    previewContainer.appendChild(img);

                    // Mettre à jour l'image principale avec la première image de cette couleur
                    if (index === 0) {
                        updateMainImageFromColor(colorName);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Fonction pour mettre à jour l'image principale à partir d'une couleur
function updateMainImageFromColor(colorName) {
    const previewContainer = document.getElementById(`preview-${colorName}`);
    if (!previewContainer) return;

    const firstImage = previewContainer.querySelector('img');
    if (!firstImage) return;

    const mainImagePreview = document.getElementById('mainImagePreview');
    const mainImagePreviewContainer = document.getElementById('mainImagePreviewContainer');

    if (mainImagePreview && mainImagePreviewContainer) {
        mainImagePreview.src = firstImage.src;
        mainImagePreview.alt = `Image ${colorName}`;
        mainImagePreviewContainer.classList.remove('hidden');

        // Afficher un message avec le nom de la couleur
        const imageLabel = document.querySelector('label[for="mainImageInput"]');
        if (imageLabel) {
            imageLabel.innerHTML = `صورة المنتج الرئيسية <span class="text-sm text-gray-500">(${colorName})</span>`;
        }
    }
}

// Fonction pour prévisualiser les images des couleurs personnalisées
function previewCustomColorImages(input, colorName) {
    const previewContainer = document.getElementById(`custom-preview-${colorName}`);
    if (!previewContainer) return;

    previewContainer.innerHTML = '';

    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'image-preview';
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview ${index + 1}">`;
                    previewContainer.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Fonction pour supprimer une image existante
function removeExistingImage(colorName, imagePath) {
    if (confirm('هل أنت متأكد من حذف هذه الصورة؟')) {
        // Ajouter à la liste des images supprimées
        removedImages.push({color: colorName, image: imagePath});

        // Supprimer l'élément du DOM
        event.target.closest('.relative').remove();

        console.log('Image supprimée:', {color: colorName, image: imagePath});
    }
}

// Fonction pour ajouter une couleur personnalisée
function addCustomColor() {
    const colorPicker = document.getElementById('newColorPicker');
    const colorNameInput = document.getElementById('newColorName');
    const customColorsContainer = document.getElementById('customColorsContainer');
    const colorPreview = document.getElementById('colorPreview');

    if (!colorNameInput.value.trim()) {
        alert('من فضلك أدخل اسمًا لللون');
        return;
    }

    const colorName = colorNameInput.value.trim();
    const colorHex = colorPicker.value;

    // Vérifier si la couleur existe déjà
    const exists = Array.from(document.querySelectorAll('input[name="couleurs[]"]'))
        .some(el => el.value.toLowerCase() === colorName.toLowerCase());
    if (exists) {
        alert('هذه اللون موجودة بالفعل.');
        return;
    }

    // Créer l'élément de couleur personnalisée
    const colorElement = document.createElement('div');
    colorElement.className = 'bg-white border-2 border-purple-200 rounded-xl p-4 hover:shadow-lg transition-all duration-300 custom-color-item';
    colorElement.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <input type="hidden" name="couleurs[]" value="${colorName}">
                <input type="hidden" name="couleurs_hex[]" value="${colorHex}">
                <div class="w-12 h-12 rounded-full border-3 border-gray-300 shadow-md" style="background-color: ${colorHex}"></div>
                <div>
                    <span class="text-lg font-medium text-gray-700">${colorName}</span>
                    <div class="text-sm text-gray-500">لون مخصص</div>
                </div>
            </div>
            <button type="button" onclick="removeCustomColor(this)"
                    class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-full transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-600">المخزون:</label>
                <input type="number" name="stock_couleur_custom_${customColorCounter}"
                       placeholder="0" min="0"
                       class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-gray-50"
                       value="0"
                       onchange="calculateTotalStock()">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600 block mb-1">صور هذا اللون:</label>
                <input type="file" name="custom_color_images_${customColorCounter}[]"
                       accept="image/*" multiple
                       class="w-full text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       onchange="previewCustomColorImages(this, '${colorName}')">
                <div class="custom-color-image-preview mt-2" id="custom-preview-${colorName}"></div>
            </div>
        </div>
    `;

    customColorsContainer.appendChild(colorElement);

    // Réinitialiser les inputs
    colorNameInput.value = '';
    colorPicker.value = '#ef4444';
    colorPreview.style.backgroundColor = '#ef4444';

    // Recalculer le stock total
    calculateTotalStock();
    updateSelectedColorsCount();

    customColorCounter++;
}



// Fonction pour calculer le stock total
function calculateTotalStock() {
    let total = 0;

    // Calculer le stock des couleurs prédéfinies
    const predefinedStockInputs = document.querySelectorAll('input[name^="stock_couleur_"]:not([name*="custom"])');
    predefinedStockInputs.forEach((input) => {
        const colorCard = input.closest('.color-card');
        const checkbox = colorCard ? colorCard.querySelector('input[name="couleurs[]"]') : null;

        if (checkbox && checkbox.checked) {
            const value = parseInt(input.value) || 0;
            total += value;
        }
    });

    // Calculer le stock des couleurs personnalisées
    const customStockInputs = document.querySelectorAll('input[name^="stock_couleur_custom_"]');
    customStockInputs.forEach((input) => {
        const value = parseInt(input.value) || 0;
        total += value;
    });

    // Mettre à jour l'affichage
    const stockTotal = document.getElementById('stockTotal');
    const stockTotalHidden = document.getElementById('stockTotalHidden');

    if (stockTotal) {
        stockTotal.value = total;
        // Effet visuel
        stockTotal.style.backgroundColor = '#d1fae5';
        setTimeout(() => {
            stockTotal.style.backgroundColor = '#f9fafb';
        }, 300);
    }

    if (stockTotalHidden) {
        stockTotalHidden.value = total;
    }

    return total;
}

// Fonction pour mettre à jour le compteur de couleurs sélectionnées
function updateSelectedColorsCount() {
    const selectedColors = document.querySelectorAll('input[name="couleurs[]"]:checked');
    const countElement = document.getElementById('selectedColorsCount');
    if (countElement) {
        countElement.textContent = `${selectedColors.length} محددة`;
    }
}

// Fonction pour ajouter une taille personnalisée
function addCustomSize() {
    const input = document.getElementById('customSizeInput');
    const container = document.getElementById('customSizesContainer');

    if (!input || !container) return;

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
    sizeElement.className = 'flex items-center space-x-2 bg-green-50 border border-green-200 rounded-lg px-3 py-2 mb-2';
    sizeElement.innerHTML = `
        <span class="text-sm font-medium text-green-800">${value}</span>
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

// Fonction pour réinitialiser le formulaire
function resetForm() {
    if (confirm('هل أنت متأكد من إعادة تعيين النموذج؟ سيتم فقدان جميع التغييرات غير المحفوظة.')) {
        window.location.reload();
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('Formulaire moderne de modification de produits chargé');

    // Initialiser la prévisualisation des couleurs
    const colorPicker = document.getElementById('newColorPicker');
    const colorPreview = document.getElementById('colorPreview');

    if (colorPicker && colorPreview) {
        colorPreview.style.backgroundColor = colorPicker.value;
        colorPicker.addEventListener('change', function() {
            colorPreview.style.backgroundColor = this.value;
        });
    }

    // Gestion conditionnelle des tailles selon la catégorie
    const categorieSelect = document.getElementById('categorie_id');
    const taillesSection = document.getElementById('taillesSection');
    const taillesRequired = document.getElementById('taillesRequired');
    const tailleCheckboxes = document.querySelectorAll('.taille-checkbox');

    function toggleTaillesSection() {
        if (categorieSelect && categorieSelect.value) {
            const selectedOption = categorieSelect.options[categorieSelect.selectedIndex];
            const categoryText = selectedOption.text.toLowerCase();
            const isAccessoire = categoryText.includes('accessoire');

            if (isAccessoire) {
                taillesSection.style.display = 'none';
                taillesRequired.style.display = 'none';
                tailleCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.removeAttribute('name');
                    checkbox.disabled = true;
                });
            } else {
                taillesSection.style.display = 'block';
                taillesRequired.style.display = 'inline';
                tailleCheckboxes.forEach(checkbox => {
                    checkbox.setAttribute('name', 'tailles[]');
                    checkbox.disabled = false;
                });
            }
        }
    }

    if (categorieSelect) {
        categorieSelect.addEventListener('change', toggleTaillesSection);
        toggleTaillesSection();
    }

    // Validation du formulaire
    const form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const selectedColors = document.querySelectorAll('input[name="couleurs[]"]:checked');

            if (selectedColors.length === 0) {
                e.preventDefault();
                alert('من فضلك حدد لونًا واحدًا على الأقل');
                return;
            }

            // Ajouter les images supprimées au formulaire
            if (removedImages.length > 0) {
                const removedImagesInput = document.createElement('input');
                removedImagesInput.type = 'hidden';
                removedImagesInput.name = 'removed_images';
                removedImagesInput.value = JSON.stringify(removedImages);
                form.appendChild(removedImagesInput);
            }

            console.log('Formulaire soumis avec succès');
        });
    }

    // Initialiser les compteurs
    updateSelectedColorsCount();
    calculateTotalStock();

    // Gestion des couleurs masquées
    initializeHiddenColors();
});

// Fonction pour initialiser la gestion des couleurs masquées
function initializeHiddenColors() {
    document.querySelectorAll('input[name="hidden_colors[]"]').forEach(checkbox => {
        const label = checkbox.closest('label');

        label.addEventListener('click', function(e) {
            e.preventDefault();
            checkbox.checked = !checkbox.checked;

            // Mettre à jour l'apparence
            if (checkbox.checked) {
                label.classList.remove('border-gray-200', 'bg-white');
                label.classList.add('border-red-300', 'bg-red-50');
                label.querySelector('span').classList.remove('text-gray-700');
                label.querySelector('span').classList.add('text-red-600');
                label.querySelector('div').classList.add('opacity-50');

                // Ajouter l'icône
                if (!label.querySelector('.fa-eye-slash')) {
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-eye-slash text-red-500 text-xs';
                    label.appendChild(icon);
                }
            } else {
                label.classList.remove('border-red-300', 'bg-red-50');
                label.classList.add('border-gray-200', 'bg-white');
                label.querySelector('span').classList.remove('text-red-600');
                label.querySelector('span').classList.add('text-gray-700');
                label.querySelector('div').classList.remove('opacity-50');

                // Supprimer l'icône
                const icon = label.querySelector('.fa-eye-slash');
                if (icon) {
                    icon.remove();
                }
            }
        });
    });
}
</script>
@endsection
