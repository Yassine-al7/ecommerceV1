@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
                    إضافة منتج جديد
                </h1>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>رجوع
                </a>
            </div>

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-8" id="productForm" onsubmit="return validateProductForm()">
                @csrf

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
                            <input type="text" name="name" value="{{ old('name') }}" required
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
                                    <option value="{{ $category->id }}" @selected(old('categorie_id') == $category->id)>
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
                                <div id="mainImagePreviewContainer" class="mt-3 hidden">
                                    <img id="mainImagePreview" class="w-32 h-32 object-cover rounded-lg border border-gray-300" alt="Prévisualisation">
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

                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                                @php
                                    $predefinedColors = [
                                        'Rouge' => '#ef4444', 'Vert' => '#22c55e', 'Bleu' => '#3b82f6', 'Jaune' => '#eab308',
                                        'Orange' => '#f97316', 'Violet' => '#8b5cf6', 'Rose' => '#ec4899', 'Marron' => '#a3a3a3',
                                        'Noir' => '#000000', 'Blanc' => '#ffffff', 'Gris' => '#6b7280', 'Beige' => '#d4af37',
                                        'Turquoise' => '#06b6d4', 'Or' => '#fbbf24', 'Argent' => '#9ca3af', 'Bordeaux' => '#7c2d12'
                                    ];

                                    // Récupérer les couleurs sélectionnées (pour old values)
                                    $selectedColors = old('couleurs', []);
                                    $colorIndex = 0; // Index pour les champs de stock
                                @endphp
                                @foreach($predefinedColors as $name => $hex)
                                    @php
                                        $isSelected = in_array($name, $selectedColors);
                                        $currentIndex = $isSelected ? $colorIndex : null;
                                        if ($isSelected) $colorIndex++;
                                    @endphp
                                    <div class="color-card bg-white border-2 border-gray-200 rounded-xl p-3 hover:shadow-lg transition-all duration-300 cursor-pointer group relative flex flex-col items-center justify-between"
                                         data-color-name="{{ $name }}" data-color-hex="{{ $hex }}"
                                         style="min-height: 160px;"
                                         onclick="if(event.target.type !== 'checkbox' && event.target.tagName !== 'INPUT') this.querySelector('input[type=checkbox]').click()">
                                        
                                        <!-- Checkbox (Top Left) -->
                                        <div class="absolute top-3 left-3 z-10">
                                            <input type="checkbox" name="couleurs[]" value="{{ $name }}"
                                                   @checked(in_array($name, old('couleurs', [])))
                                                   data-hex="{{ $hex }}"
                                                   class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 color-checkbox cursor-pointer"
                                                   onchange="toggleColorCard(this)">
                                            
                                            <!-- Check icon for visual feedback when selected -->
                                            <div class="hidden check-icon absolute top-0 left-0 w-5 h-5 bg-purple-600 rounded text-white items-center justify-center pointer-events-none">
                                                <i class="fas fa-check text-xs"></i>
                                            </div>
                                        </div>

                                        <input type="hidden" name="couleurs_hex[]" value="{{ $hex }}" class="color-hex-input">

                                        <!-- Content Wrapper -->
                                        <div class="flex flex-col items-center justify-center w-full mt-2 h-full"> 
                                            <!-- Color Circle -->
                                            <div class="w-16 h-16 rounded-full shadow-md color-preview group-hover:scale-110 transition-transform duration-200 mb-3 border-2 border-gray-100"
                                                 style="background-color: {{ $hex }}"></div>

                                            <!-- Name -->
                                            <span class="text-base font-semibold text-gray-700 text-center color-name block">{{ $name }}</span>
                                        </div>

                                        <!-- Stock Input (Visible only if selected) -->
                                        <div class="w-full stock-field mt-3 pt-2 border-t border-gray-100" style="display: none;">
                                            <label class="block text-xs font-medium text-gray-500 mb-1 text-center">المخزون</label>
                                            <input type="number"
                                                   name="stock_couleur_{{ $loop->index }}"
                                                   value="{{ old('stock_couleur_' . $loop->index, 0) }}"
                                                   min="0"
                                                   class="w-full px-2 py-1.5 text-center text-sm font-medium border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 stock-input bg-white shadow-sm"
                                                   placeholder="0"
                                                   onclick="event.stopPropagation()"
                                                   oninput="calculateTotalStock()">
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Custom Add Button -->
                                <div class="color-card bg-purple-50 border-2 border-dashed border-purple-400 rounded-xl p-3 hover:bg-purple-100 transition-all duration-300 cursor-pointer group flex flex-col items-center justify-center relative"
                                     style="min-height: 160px;"
                                     onclick="document.getElementById('customColorModal').classList.remove('hidden')">
                                    <div class="w-12 h-12 rounded-full bg-white border-2 border-purple-100 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                                        <i class="fas fa-plus text-purple-600 text-xl"></i>
                                    </div>
                                    <span class="text-base font-bold text-purple-700 text-center">إضافة لون<br>مخصص</span>
                                </div>
                            </div>

                            <!-- Modal for Custom Color -->
                            <div id="customColorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                                <div class="bg-white rounded-xl p-6 w-80 shadow-2xl transform transition-all scale-100">
                                    <h3 class="text-lg font-bold text-gray-800 mb-4 text-center">إضافة لون جديد</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم اللون</label>
                                            <input type="text" id="customColorName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none" placeholder="مثال: ذهبي">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">اللون</label>
                                            <div class="flex items-center space-x-3">
                                                <input type="color" id="customColorPicker" class="h-10 w-10 rounded cursor-pointer border-0 p-0" value="#8b5cf6" onchange="document.getElementById('customColorHex').textContent = this.value">
                                                <span id="customColorHex" class="text-sm text-gray-500 font-mono">#8b5cf6</span>
                                            </div>
                                        </div>
                                        <div class="flex justify-end space-x-2 pt-2">
                                            <button type="button" onclick="document.getElementById('customColorModal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">إلغاء</button>
                                            <button type="button" onclick="addNewCustomColor()" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">إضافة</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>





                        <!-- Résumé des couleurs sélectionnées -->
                        <div id="selectedColorsSummary" class="bg-green-50 border border-green-200 rounded-xl p-4" style="display: none;">
                            <h4 class="text-sm font-semibold text-green-800 mb-3 flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                الألوان المحددة:
                            </h4>
                            <div id="selectedColorsList" class="flex flex-wrap gap-2">
                                <!-- Les couleurs sélectionnées seront affichées ici -->
                            </div>

                            <!-- Stock total -->
                            <div class="mt-4 pt-4 border-t border-green-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-green-800">المخزون الإجمالي:</span>
                                    <span id="totalStockDisplay" class="text-lg font-bold text-green-900">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('couleurs')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Stock Global -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                    <h2 class="text-xl font-semibold text-blue-800 mb-6 flex items-center">
                        <i class="fas fa-boxes mr-3"></i>
                        المخزون الإجمالي
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="quantite_stock" class="block text-sm font-medium text-gray-700 mb-2">
                                عدد القطع المتاحة <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="quantite_stock" name="quantite_stock"
                                   min="0" step="1"
                                   value="{{ old('quantite_stock', 0) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-lg font-medium"
                                   placeholder="سيتم ملؤه تلقائياً"
                                   readonly>
                            <p class="text-sm text-gray-500 mt-2">
                                💡 <strong>نصيحة:</strong> هذا الحقل يتم ملؤه تلقائياً بناءً على الكميات المدخلة لكل لون.
                            </p>
                            @error('quantite_stock')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
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

                <!-- Tailles -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200" id="taillesSection">
                    <h2 class="text-xl font-semibold text-green-800 mb-6 flex items-center">
                        <i class="fas fa-ruler mr-3"></i>
                        المقاسات المتاحة <span id="taillesRequired" class="text-red-500">*</span>
                    </h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
                        @php
                            $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'];
                        @endphp
                        @foreach($sizes as $size)
                            <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-green-100 transition-colors">
                                <input type="checkbox" name="tailles[]" value="{{ $size }}"
                                       @checked(in_array($size, old('tailles', [])))
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
                                <input type="text" name="prix_admin" value="{{ old('prix_admin') }}" required
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
                                <input type="number" name="prix_vente" value="{{ old('prix_vente') }}" step="0.01" min="0" required
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
                        <i class="fas fa-save mr-2"></i>إنشاء المنتج
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

// Fonction pour basculer l'affichage des détails d'une couleur
function toggleColorCard(checkbox) {
    const colorCard = checkbox.closest('.color-card');
    const hexInput = colorCard.querySelector('.color-hex-input');
    const stockField = colorCard.querySelector('.stock-field');

    if (checkbox.checked) {
        colorCard.classList.add('selected');
        if (hexInput) hexInput.disabled = false;
        if (stockField) stockField.style.display = 'block';

        // Changer l'image principale du produit
        changeMainProductImage(colorCard);
    } else {
        colorCard.classList.remove('selected');
        if (hexInput) hexInput.disabled = true;
        if (stockField) stockField.style.display = 'none';
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

// Données des images existantes (vide pour le formulaire de création)
const existingColorImages = {};



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







// Fonction pour mettre à jour le compteur de couleurs sélectionnées
function updateSelectedColorsCount() {
    const selectedColors = document.querySelectorAll('input[name="couleurs[]"]:checked');
    const countElement = document.getElementById('selectedColorsCount');
    if (countElement) {
        countElement.textContent = `${selectedColors.length} محددة`;
    }

    // Calculer et afficher le stock total
    calculateTotalStock();
}

// Fonction pour calculer le stock total
function calculateTotalStock() {
    const stockInputs = document.querySelectorAll('.stock-input');
    let total = 0;

    stockInputs.forEach(input => {
        const value = parseInt(input.value) || 0;
        total += value;
    });

    const totalDisplay = document.getElementById('totalStockDisplay');
    if (totalDisplay) {
        totalDisplay.textContent = total;
    }

    // Mettre à jour le champ de stock global
    const globalStockInput = document.querySelector('input[name="quantite_stock"]');
    if (globalStockInput) {
        globalStockInput.value = total;
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

// Fonction pour ajouter une couleur personnalisée via le modal
function addNewCustomColor() {
    const nameInput = document.getElementById('customColorName');
    const colorInput = document.getElementById('customColorPicker');
    const modal = document.getElementById('customColorModal');
    
    const colorName = nameInput.value.trim();
    const colorHex = colorInput.value;
    
    if (!colorName) {
        alert('يرجى إدخال اسم اللون');
        return;
    }
    
    // Vérifier si la couleur existe déjà
    const exists = Array.from(document.querySelectorAll('input[name="couleurs[]"]'))
        .some(el => el.value.toLowerCase() === colorName.toLowerCase());
        
    if (exists) {
        alert('هذا اللون موجود بالفعل');
        return;
    }
    
    // Créer la nouvelle carte de couleur
    const timestamp = Date.now();
    const colorElement = document.createElement('div');
    colorElement.className = 'color-card bg-white border-2 border-gray-200 rounded-xl p-3 hover:shadow-lg transition-all duration-300 cursor-pointer group relative flex flex-col items-center justify-between selected w-full';
    colorElement.setAttribute('data-color-name', colorName);
    colorElement.setAttribute('data-color-hex', colorHex);
    colorElement.style.minHeight = '160px';
    colorElement.onclick = function(event) {
        if(event.target.type !== 'checkbox' && event.target.tagName !== 'INPUT') 
            this.querySelector('input[type=checkbox]').click();
    };

    colorElement.innerHTML = `
        <div class="absolute top-3 left-3 z-10">
            <input type="checkbox" name="couleurs[]" value="${colorName}" checked
                   data-hex="${colorHex}"
                   class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 color-checkbox cursor-pointer"
                   onchange="toggleColorCard(this)">
        </div>

        <input type="hidden" name="couleurs_hex[]" value="${colorHex}" class="color-hex-input">

        <div class="flex flex-col items-center justify-center w-full mt-2 h-full">
            <div class="w-16 h-16 rounded-full shadow-md color-preview group-hover:scale-110 transition-transform duration-200 mb-3 border-2 border-gray-100"
                 style="background-color: ${colorHex}"></div>
            <span class="text-base font-semibold text-gray-700 text-center color-name block">${colorName}</span>
        </div>

        <div class="w-full stock-field mt-3 pt-2 border-t border-gray-100" style="display: block;">
            <label class="block text-xs font-medium text-gray-500 mb-1 text-center">المخزون</label>
            <input type="number"
                   name="stock_couleur_custom_${timestamp}"
                   value="0"
                   min="0"
                   class="w-full px-2 py-1.5 text-center text-sm font-medium border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 stock-input bg-white shadow-sm"
                   placeholder="0"
                   onclick="event.stopPropagation()"
                   oninput="calculateTotalStock()">
        </div>
    `;

    // Insérer avant le bouton d'ajout (le dernier élément de la grille)
    const addButton = document.querySelector('.color-card[onclick*="customColorModal"]');
    addButton.parentNode.insertBefore(colorElement, addButton);
    
    // Fermer le modal et réinitialiser
    modal.classList.add('hidden');
    nameInput.value = '';
    colorInput.value = '#8b5cf6';
    document.getElementById('customColorHex').textContent = '#8b5cf6';
    
    updateSelectedColorsCount();
}

// Fonction pour calculer le stock total
function calculateTotalStock() {
    const stockInputs = document.querySelectorAll('.stock-input');
    let total = 0;

    stockInputs.forEach(input => {
        const card = input.closest('.color-card');
        if (card && card.classList.contains('selected')) {
             const value = parseInt(input.value) || 0;
             total += value;
        }
    });

    const quantiteStock = document.getElementById('quantite_stock');
    if (quantiteStock) {
        quantiteStock.value = total;
    }
    
    const totalDisplay = document.getElementById('totalStockDisplay');
    if (totalDisplay) {
        totalDisplay.textContent = total;
    }
}

// Fonction pour réinitialiser le formulaire
function resetForm() {
    if (confirm('هل أنت متأكد من إعادة تعيين النموذج؟ سيتم فقدان جميع البيانات المدخلة.')) {
        document.getElementById('productForm').reset();

        // Réinitialiser l'affichage des couleurs
        document.querySelectorAll('.color-card').forEach(card => {
            card.classList.remove('selected');
            const details = card.querySelector('.color-details');
            if (details) {
                details.classList.add('hidden');
                details.style.display = 'none';
            }
        });

        // Vider les conteneurs
        document.getElementById('customSizesContainer').innerHTML = '';

        // Réinitialiser les compteurs
        updateSelectedColorsCount();
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('Formulaire moderne de création de produits chargé');



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

            console.log('Formulaire soumis avec succès');
        });
    }

    // Initialiser les compteurs
    updateSelectedColorsCount();
});

// Fonction de validation du formulaire de produit
function validateProductForm() {
    console.log('🔍 Validation du formulaire de produit en cours...');

    const form = document.getElementById('productForm');

    // Debug: Afficher tous les éléments de couleur
    console.log('🔍 Debug des couleurs:');
    console.log('- .color-item:', document.querySelectorAll('.color-item').length);
    console.log('- .color-item.selected:', document.querySelectorAll('.color-item.selected').length);
    console.log('- [data-selected="true"]:', document.querySelectorAll('[data-selected="true"]').length);
    console.log('- .selected-color:', document.querySelectorAll('.selected-color').length);
    console.log('- input[name^="couleurs"]:', document.querySelectorAll('input[name^="couleurs"]').length);
    console.log('- input[name^="couleurs"]:checked:', document.querySelectorAll('input[name^="couleurs"]:checked').length);
    const name = form.querySelector('input[name="name"]').value;
    const category = form.querySelector('select[name="categorie_id"]').value;
    const prixAdmin = form.querySelector('input[name="prix_admin"]').value;
    const prixVente = form.querySelector('input[name="prix_vente"]').value;
    const quantiteStock = form.querySelector('input[name="quantite_stock"]').value;

    console.log('📝 Données du formulaire:', {
        name: name,
        category: category,
        prixAdmin: prixAdmin,
        prixVente: prixVente,
        quantiteStock: quantiteStock
    });

    // Vérifier les champs requis
    if (!name.trim()) {
        console.log('❌ Nom du produit manquant');
        alert('يرجى إدخال اسم المنتج');
        return false;
    }

    if (!category) {
        console.log('❌ Catégorie manquante');
        alert('يرجى اختيار تصنيف');
        return false;
    }

    if (!prixAdmin.trim()) {
        console.log('❌ Prix admin manquant');
        alert('يرجى إدخال سعر الإدارة');
        return false;
    }

    if (!prixVente || prixVente <= 0) {
        console.log('❌ Prix vente invalide');
        alert('يرجى إدخال سعر بيع صحيح');
        return false;
    }

    if (!quantiteStock || quantiteStock < 0) {
        console.log('❌ Quantité stock invalide');
        alert('يرجى إدخال كمية مخزون صحيحة');
        return false;
    }

    // Vérifier les couleurs - utiliser les checkboxes
    const colorCheckboxes = document.querySelectorAll('input[name="couleurs[]"]:checked');
    const customColorCheckboxes = document.querySelectorAll('input[name="couleurs_personnalisees[]"]:checked');

    console.log('🎨 Checkboxes couleurs cochées:', colorCheckboxes.length);
    console.log('🎨 Checkboxes couleurs personnalisées cochées:', customColorCheckboxes.length);

    if (colorCheckboxes.length === 0 && customColorCheckboxes.length === 0) {
        console.log('❌ Aucune couleur sélectionnée');
        alert('يرجى اختيار لون واحد على الأقل');
        return false;
    }

    console.log('✅ Couleurs sélectionnées:', colorCheckboxes.length + customColorCheckboxes.length);

    console.log('✅ Formulaire valide, soumission en cours...');
    return true;
}

</script>
@endsection
