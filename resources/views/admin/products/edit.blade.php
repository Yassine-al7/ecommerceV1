@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">تعديل المنتج</h1>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>رجوع
                </a>
            </div>

            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du produit -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المنتج *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">الألوان *</label>
                        <div class="space-y-4">
                            <!-- Couleurs prédéfinies -->
                            <div>
                                <p class="text-xs text-gray-600 mb-3 flex items-center">
                                    <i class="fas fa-palette mr-2 text-blue-600"></i>
                                    الألوان المسبقة: <span id="selectedColorsCount" class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">0 محددة</span>
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    @php
                                        $predefinedColors = [
                                            'Rouge' => '#ff0000', 'Vert' => '#00ff00', 'Bleu' => '#0000ff', 'Jaune' => '#ffff00',
                                            'Orange' => '#ffa500', 'Violet' => '#800080', 'Rose' => '#ffc0cb', 'Marron' => '#a52a2a',
                                            'Noir' => '#000000', 'Blanc' => '#ffffff', 'Gris' => '#808080', 'Beige' => '#f5f5dc',
                                            'Turquoise' => '#40e0d0', 'Or' => '#ffd700', 'Argent' => '#c0c0c0', 'Bordeaux' => '#800020'
                                        ];
                                        // Récupérer les couleurs depuis le produit
                                        $rawColors = is_string($product->couleur) ? json_decode($product->couleur, true) ?? [] : (is_array($product->couleur) ? $product->couleur : []);

                                        // Récupérer les stocks par couleur pour déterminer quelles couleurs sont actives
                                        $stockCouleurs = $product->stock_couleurs ?: [];
                                        $activeColors = [];
                                        $customColors = [];
                                        $stockByColor = []; // Stock par couleur pour l'affichage

                                        // Créer une liste des couleurs actives basée sur les stocks
                                        foreach ($stockCouleurs as $stockColor) {
                                            if (is_array($stockColor) && isset($stockColor['name'])) {
                                                $colorName = $stockColor['name'];
                                                $stockQuantity = $stockColor['quantity'] ?? 0;

                                                // Ajouter aux couleurs actives si elle a un stock défini (même 0)
                                                $activeColors[] = $colorName;

                                                // Stocker la quantité pour l'affichage
                                                $stockByColor[$colorName] = $stockQuantity;

                                                // Vérifier si c'est une couleur personnalisée
                                                if (!in_array($colorName, array_keys($predefinedColors))) {
                                                    // Chercher la couleur hex dans le champ couleur
                                                    $colorHex = null;
                                                    foreach ($rawColors as $rawColor) {
                                                        if (is_array($rawColor) && isset($rawColor['name']) && $rawColor['name'] === $colorName) {
                                                            $colorHex = $rawColor['hex'] ?? null;
                                                            break;
                                                        }
                                                    }
                                                    $customColors[] = ['name' => $colorName, 'hex' => $colorHex];
                                                }
                                            }
                                        }
                                    @endphp
                                    @foreach($predefinedColors as $colorName => $colorHex)
                                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300 color-card" data-color-name="{{ $colorName }}">
                                            <div class="flex items-center justify-between mb-3">
                                                <label class="flex items-center space-x-3 cursor-pointer flex-1">
                                                    <input type="checkbox" name="couleurs_predefinies[]" value="{{ $colorName }}"
                                                           @checked(in_array($colorName, old('couleurs_predefinies', $activeColors)))
                                                           data-hex="{{ $colorHex }}"
                                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 color-checkbox"
                                                           onchange="updateColorHex(this)">
                                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm color-preview" style="background-color: {{ $colorHex }}"></div>
                                                    <span class="text-sm font-medium text-gray-700 color-name">{{ $colorName }}</span>
                                                </label>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <label class="text-xs font-medium text-gray-600">المخزون:</label>
                                                <input type="number" name="stock_couleur_{{ $loop->index }}"
                                                       placeholder="0" min="0"
                                                       class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 stock-input"
                                                       value="{{ old('stock_couleur_' . $loop->index, $stockByColor[$colorName] ?? 0) }}"
                                                       data-original-value="{{ old('stock_couleur_' . $loop->index, $stockByColor[$colorName] ?? 0) }}"
                                                       data-color-name="{{ $colorName }}"
                                                       onchange="updateSelectedColorsCount(); detectStockChange(this)"
                                                       oninput="detectStockChange(this)">
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
                                            <input type="color" id="customColorPicker" value="#ff6b6b"
                                                   class="w-14 h-12 border-2 border-gray-300 rounded-lg cursor-pointer shadow-sm">
                                            <div id="colorPreview" class="w-10 h-10 rounded-full border-2 border-gray-300 shadow-sm"
                                                 style="background-color: #ff6b6b;"></div>
                                        </div>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="text-xs font-medium text-blue-700 mb-2 block">اسم اللون</label>
                                        <input type="text" id="customColorName" placeholder="مثال: مرجاني، نيلي، بحري..."
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" onclick="addCustomColor()"
                                                class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 hover:shadow-md font-medium">
                                            <i class="fas fa-plus mr-2"></i>إضافة
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="flex items-center space-x-2 text-xs text-gray-600">
                                        <input type="checkbox" id="forceCustomName" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                                        <span>فرض استخدام الاسم المخصص (تجاهل الملء التلقائي)</span>
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
                                            <label class="text-xs font-medium text-gray-600">المخزون:</label>
                                            <input type="number" name="stock_couleur_custom_{{ $loop->index }}"
                                                   placeholder="0" min="0"
                                                   class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                                                   value="{{ old('stock_couleur_custom_' . $loop->index, $stockByColor[$customColor['name']] ?? 0) }}"
                                                   data-original-value="{{ old('stock_couleur_custom_' . $loop->index, $stockByColor[$customColor['name']] ?? 0) }}"
                                                   data-color-name="{{ $customColor['name'] }}"
                                                   onchange="detectStockChange(this)"
                                                   oninput="detectStockChange(this)">
                                        </div>
                                        <input type="hidden" name="couleurs_personnalisees[]" value="{{ $customColor['name'] }}">
                                    </div>
                                @endforeach
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">المقاسات المتاحة <span id="taillesRequired" class="text-red-500">*</span></label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">سعر المشرف (MAD) *</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">سعر البيع (MAD) *</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">الكمية الإجمالية في المخزون</label>
                        <input type="number" id="stockTotal" value="0" min="0" readonly
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-semibold">
                        <p class="text-xs text-gray-500 mt-1">💡 يُحسب تلقائيًا: مجموع مخزون جميع الألوان</p>
                        <input type="hidden" name="quantite_stock" id="stockTotalHidden" value="0">
                        @error('quantite_stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">صورة المنتج</label>
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
                        <i class="fas fa-save mr-2"></i>تحديث
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
        alert('من فضلك أدخل اسمًا لللون');
        return;
    }

    const colorName = colorNameInput.value.trim();
    const colorHex = colorPicker.value;

    console.log('=== DEBUG AJOUT COULEUR ===');
    console.log('اسم اللون (الأصلي):', colorNameInput.value);
    console.log('اسم اللون (التنظيف):', colorName);
    console.log('طول اسم اللون:', colorName.length);
    console.log('اللون السداسي:', colorHex);
    console.log('اسم اللون المشفر JSON:', JSON.stringify(colorName));
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
            <label class="text-xs font-medium text-gray-600">المخزون:</label>
            <input type="number" name="stock_couleur_custom_${customColorCounter}"
                   placeholder="0" min="0"
                   class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                   value="0"
                   data-original-value="0"
                   data-color-name="${colorName}"
                   onchange="detectStockChange(this)"
                   oninput="detectStockChange(this)">
        </div>
        <input type="hidden" name="couleurs_personnalisees[]" value="${colorName}">
    `;

    container.appendChild(colorItem);

    console.log('العنصر المنتج للون منشأ:', colorItem.outerHTML); // تصحيح للعنصر المنشأ
    console.log('اللون إضاف إلى المحتوى:', container.children.length); // تصحيح لعدد العناصر

    // Réinitialiser seulement الاسم، حفظ اللون المحدد
    colorNameInput.value = '';
    // لا تعيد تهيئة اللون المحدد للتصفير

    // تحديث حقل الألوان المدمجة
    updateCombinedColors();

    // إضافة سمات الاستماع للمخزون
    const stockInput = colorItem.querySelector('input[name^="stock_couleur_custom_"]');
    if (stockInput) {
        stockInput.addEventListener('input', calculateTotalStock);
    }

    // حساب المخزون الإجمالي
    calculateTotalStock();

    customColorCounter++;
}

// دالة للتعامل مع ألوان الألوان المسبقة
function updateColorHex(checkbox) {
    const hexValue = checkbox.getAttribute('data-hex');
    const existingHexInput = document.querySelector(`input[type="hidden"][name="couleurs_hex[]"][value="${hexValue}"]`);

    if (checkbox.checked && !existingHexInput) {
        // إضافة حقل hex إذا كان اللون محدد
        const hexInput = document.createElement('input');
        hexInput.type = 'hidden';
        hexInput.name = 'couleurs_hex[]';
        hexInput.value = hexValue;
        hexInput.setAttribute('data-color', checkbox.value);
        checkbox.parentElement.appendChild(hexInput);
    } else if (!checkbox.checked && existingHexInput) {
        // إزالة حقل hex إذا كان اللون غير محدد
        existingHexInput.remove();
    }

    // تحديث عرض الألوان المحددة
    updateSelectedColorsCount();
    updateSelectedColorsSummary();

    // تحديث حقل الألوان المدمجة
    updateCombinedColors();
}

// إزالة لون مخصص
function removeCustomColor(button) {
    button.closest('.custom-color-item').remove();
    // تحديث حقل الألوان المدمجة
    updateCombinedColors();

    // تحديث عرض الألوان المحددة
    updateSelectedColorsCount();
    updateSelectedColorsSummary();
}

// تحديث حقل الألوان المدمجة
function updateCombinedColors() {
    // تعيين ألوان الألوان المسبقة إلى قيم سداسية
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

    // تفريغ حاوية الألوان المدمجة
    const container = document.getElementById('couleursCombinedContainer');
    if (container) {
        container.innerHTML = '';

        // إنشاء حقل خفي لكل لون مع قيمته السداسية
        allColors.forEach(color => {
            // حقل لاسم اللون
            const colorInput = document.createElement('input');
            colorInput.type = 'hidden';
            colorInput.name = 'couleurs[]';
            colorInput.value = color;
            container.appendChild(colorInput);

            // حقل للقيمة السداسية
            const hexInput = document.createElement('input');
            hexInput.type = 'hidden';
            hexInput.name = 'couleurs_hex[]';

            // تحديد قيمة اللون السداسي
            if (predefinedColorsHex[color]) {
                // اللون المسبق
                hexInput.value = predefinedColorsHex[color];
            } else {
                // اللون المخصص - جرب استرجاعه من العنصر المخصص
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

        console.log('الحقول المحدثة حاليًا في الوقت الفعلي:', allColors);
        console.log('عدد المدخلات المنشأة:', container.children.length);
    }
}

// 🆕 دالة للكشف عن تغييرات المخزون في الوقت الفعلي
function detectStockChange(input) {
    const originalValue = parseInt(input.getAttribute('data-original-value') || '0');
    const currentValue = parseInt(input.value) || 0;
    const colorName = input.getAttribute('data-color-name');

    // التأكد من تغيير القيمة
    if (currentValue !== originalValue) {
        // إضافة صفات بصرية للإشارة إلى التغيير
        input.classList.add('border-yellow-400', 'bg-yellow-50');
        input.classList.remove('border-gray-300', 'bg-gray-50');

        // عرض إشارة التعديل
        const changeIndicator = input.parentElement.querySelector('.change-indicator') || createChangeIndicator(input.parentElement);
        changeIndicator.style.display = 'block';
        changeIndicator.textContent = `${originalValue} → ${currentValue}`;

        console.log(`🔄 المخزون تم تغييره لـ ${colorName}: ${originalValue} → ${currentValue}`);
    } else {
        // إزالة صفات التعديل إذا عادت القيمة إلى الأصل
        input.classList.remove('border-yellow-400', 'bg-yellow-50');
        input.classList.add('border-gray-300', 'bg-gray-50');

        // إخفاء إشارة التعديل
        const changeIndicator = input.parentElement.querySelector('.change-indicator');
        if (changeIndicator) {
            changeIndicator.style.display = 'none';
        }
    }

    // حساب المخزون الإجمالي
    calculateTotalStock();
}

// 🆕 دالة لإنشاء إشارة التغيير
function createChangeIndicator(parentElement) {
    const indicator = document.createElement('div');
    indicator.className = 'change-indicator text-xs text-yellow-700 bg-yellow-100 px-2 py-1 rounded mt-1';
    indicator.style.display = 'none';
    parentElement.appendChild(indicator);
    return indicator;
}







// 🆕 دالة لتحديث إشارة التغييرات
function updateChangesIndicator() {
    const stockInputs = document.querySelectorAll('input[name^="stock_couleur"]');
    const changesIndicator = document.getElementById('changesIndicator');

    if (!changesIndicator) return;

    let changesCount = 0;
    let totalDifference = 0;

    stockInputs.forEach(input => {
        const originalValue = parseInt(input.getAttribute('data-original-value') || '0');
        const currentValue = parseInt(input.value) || 0;

        if (currentValue !== originalValue) {
            changesCount++;
            totalDifference += (currentValue - originalValue);
        }
    });

    if (changesCount === 0) {
        changesIndicator.textContent = 'لا توجد تغييرات';
        changesIndicator.className = 'text-xs text-gray-500';
    } else {
        const sign = totalDifference > 0 ? '+' : '';
        changesIndicator.textContent = `${changesCount} تغيير(${sign}${totalDifference} وحدة)`;
        changesIndicator.className = 'text-xs text-orange-600 font-medium';
    }
}



// دالة لتحديث المعاينة واسم اللون
function updateColorPreview() {
    const colorPicker = document.getElementById('customColorPicker');
    const colorNameInput = document.getElementById('customColorName');
    const colorPreview = document.getElementById('colorPreview');

    if (!colorPicker || !colorPreview) return;

    const selectedColor = colorPicker.value;

    // تحديث المعاينة في الوقت الفعلي
    colorPreview.style.backgroundColor = selectedColor;

    // إضافة إلى حقل الاسم تلقائيًا فقط إذا كان الحقل فارغًا
    const colorNames = {
        '#ff0000': 'اللون الأحمر', '#00ff00': 'اللون الأخضر', '#0000ff': 'اللون الأزرق', '#ffff00': 'اللون الأصفر',
        '#ff00ff': 'اللون الأرجواني', '#00ffff': 'اللون الأزرق الأزرق', '#000000': 'اللون الأسود', '#ffffff': 'اللون الأبيض',
        '#ffa500': 'اللون البرتقالي', '#800080': 'اللون البنفسجي', '#ffc0cb': 'اللون الوردي', '#a52a2a': 'اللون البني',
        '#ff4500': 'اللون البرتقالي الأحمر', '#32cd32': 'اللون الأخضر الأخضر', '#4169e1': 'اللون الأزرق الملكي', '#ffd700': 'اللون الذهبي'
    };

    // التأكد من أن المستخدم يريد فرض استخدام اسم اللون المخصص
    const forceCustomName = document.getElementById('forceCustomName')?.checked || false;

    // لا تملأ إلا إذا كان الحقل فارغًا وأن المستخدم لم يقم بفرض استخدام اسم اللون المخصص
    if (colorNames[selectedColor] && (!colorNameInput.value.trim() || colorNameInput.value.trim() === '') && !forceCustomName) {
        colorNameInput.value = colorNames[selectedColor];
        console.log('اسم ملء تلقائي بـ:', colorNames[selectedColor]);
    } else if (colorNames[selectedColor] && forceCustomName) {
        console.log('اسم مخصص قوي، تجاهل الملء التلقائي');
    } else if (colorNames[selectedColor]) {
        console.log('اسم مخصص محفوظ:', colorNameInput.value.trim());
    }
}

// تهيئة في تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    console.log('نموذج تعديل الألوان مُهيئ');

    // تهيئة المعاينة مع لون المحدد في محرر اللون
    const colorPicker = document.getElementById('customColorPicker');
    const colorPreview = document.getElementById('colorPreview');
    if (colorPicker && colorPreview) {
        colorPreview.style.backgroundColor = colorPicker.value;

        // إضافة أحداث لمحرر اللون
        colorPicker.addEventListener('change', updateColorPreview);
        colorPicker.addEventListener('input', updateColorPreview);
    }

    // التأكد من تحديد ألوان على الأقل
    const selectedColors = document.querySelectorAll('input[name="couleurs_predefinies[]"]:checked');
    const customColors = document.querySelectorAll('#customColorsContainer input[name="couleurs_personnalisees[]"]');

    if (selectedColors.length === 0 && customColors.length === 0) {
        console.log('لم يتم تحديد أي لون، تحديد أول لون مسبق');
        const firstCheckbox = document.querySelector('input[name="couleurs_predefinies[]"]');
        if (firstCheckbox) {
            firstCheckbox.checked = true;
        }
    }

    // إضافة أحداث للمربعات الاختيارية للألوان المسبقة
    const predefinedCheckboxes = document.querySelectorAll('input[name="couleurs_predefinies[]"]');
    predefinedCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCombinedColors);
    });

    // تهيئة حقل الألوان المدمجة
    updateCombinedColors();

    // إدارة الحالة الشرطية للمقاسات حسب التصنيف
    const categorieSelect = document.getElementById('categorie_id');
    const taillesSection = document.getElementById('taillesSection');
    const taillesRequired = document.getElementById('taillesRequired');
    const tailleCheckboxes = document.querySelectorAll('.taille-checkbox');

    console.log('العناصر الموجودة:');
    console.log('categorieSelect:', categorieSelect);
    console.log('taillesSection:', taillesSection);
    console.log('tailleCheckboxes:', tailleCheckboxes.length);

            function toggleTaillesSection() {
        if (categorieSelect && categorieSelect.value) {
            const selectedOption = categorieSelect.options[categorieSelect.selectedIndex];
            const categoryText = selectedOption.text.toLowerCase();
            const isAccessoire = categoryText.includes('accessoire');

            console.log('التصنيف المحدد:', selectedOption.text);
            console.log('النص بالأحرف الصغيرة:', categoryText);
            console.log('هل هو إكسسوار؟', isAccessoire);

            if (isAccessoire) {
                // إخفاء قسم المقاسات للإكسسوارات
                taillesSection.style.display = 'none';
                taillesRequired.style.display = 'none';

                // إلغاء تحديد جميع المقاسات وإزالة السمة name لتجنب الإرسال
                tailleCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.removeAttribute('name');
                    checkbox.disabled = true;
                });
            } else {
                // عرض قسم المقاسات للتصنيفات الأخرى
                taillesSection.style.display = 'block';
                taillesRequired.style.display = 'inline';

                // إعادة تفعيل المدخلات وإعادة إضافة السمة name
                tailleCheckboxes.forEach(checkbox => {
                    checkbox.setAttribute('name', 'tailles[]');
                    checkbox.disabled = false;
                });
            }
        }
    }

    // الاستماع إلى تغييرات التصنيف
    if (categorieSelect) {
        categorieSelect.addEventListener('change', toggleTaillesSection);
        // تطبيق عند تحميل الصفحة الأولي
        toggleTaillesSection();
    }

    // إضافة أحداث الاستماع للمدخلات المخزون للألوان المسبقة
    const predefinedStockInputs = document.querySelectorAll('input[name^="stock_couleur_"]');
    predefinedStockInputs.forEach(input => {
        input.addEventListener('input', calculateTotalStock);
    });

    // إضافة أحداث الاستماع للمربعات الاختيارية للألوان المسبقة
    const predefinedColorCheckboxes = document.querySelectorAll('input[name="couleurs_predefinies[]"]');
    predefinedColorCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotalStock);
    });

    // تهيئة المخزون الإجمالي بالقيمة الموجودة مسبقًا للمنتج
    const initialStock = {{ $product->quantite_stock ?? 0 }};
    const stockTotal = document.getElementById('stockTotal');
    const stockTotalHidden = document.getElementById('stockTotalHidden');

    // دالة لتهيئة قيم المخزون لكل لون
    function initializeStockByColor() {
        console.log('🔍 تهيئة المخزونات لكل لون...');

        // استرجاع بيانات المخزون من المنتج
        const productStockCouleurs = @json($product->stock_couleurs ?: []);
        console.log('🔍 Stock_couleurs للمنتج:', productStockCouleurs);

        if (productStockCouleurs && Array.isArray(productStockCouleurs)) {
            // تهيئة المخزونات للألوان المسبقة
            productStockCouleurs.forEach((stockCouleur, index) => {
                const colorName = stockCouleur.name;
                const stockQuantity = stockCouleur.quantity || 0;

                console.log(`🔍 تهيئة مخزون لـ ${colorName}: ${stockQuantity}`);

                // البحث عن اللون المسبق المقابل
                console.log(`🔍 البحث عن اللون المسبق: ${colorName}`);
                const predefinedColorCard = document.querySelector(`.color-card[data-color-name="${colorName}"]`);
                console.log(`🔍 العنصر الموجود:`, predefinedColorCard);

                if (predefinedColorCard) {
                    const stockInput = predefinedColorCard.querySelector('input[name^="stock_couleur_"]');
                    if (stockInput) {
                        stockInput.value = stockQuantity;
                        console.log(`✅ مخزون متهيئ لـ ${colorName}: ${stockQuantity}`);
                    }

                    // تحديد جميع الألوان التي لديها مخزون محدد (حتى 0)
                    const checkbox = predefinedColorCard.querySelector('input[name="couleurs_predefinies[]"]');
                    if (checkbox) {
                        checkbox.checked = true;
                        if (stockQuantity > 0) {
                            console.log(`✅ اللون ${colorName} محدد`);
                            // إضافة صفة للإشارة إلى أنه في المخزون
                            predefinedColorCard.classList.add('has-stock');
                        } else {
                            console.log(`⚠️ اللون ${colorName} محدد ولكن المخزون = 0`);
                            // إضافة صفة للإشارة إلى أنه في النقص
                            predefinedColorCard.classList.add('out-of-stock');
                        }
                    }
                } else {
                    // قد يكون هذا لون مخصص
                    console.log(`🔍 اللون ${colorName} غير موجود في المسبق، البحث في المخصص...`);

                    // التأكد أولاً إذا وجد اللون بالفعل في بيانات المنتج
                    const productColors = @json($product->couleur ?: []);
                    let colorExistsInProduct = false;
                    let existingColorData = null;

                    if (Array.isArray(productColors)) {
                        for (const color of productColors) {
                            if (color.name === colorName) {
                                colorExistsInProduct = true;
                                existingColorData = color;
                                break;
                            }
                        }
                    }

                    // البحث في الألوان المخصصة التي تم عرضها
                    const customColorItems = document.querySelectorAll('.custom-color-item');
                    let foundInDOM = false;

                    customColorItems.forEach(item => {
                        const colorNameElement = item.querySelector('span');
                        if (colorNameElement && colorNameElement.textContent === colorName) {
                            const stockInput = item.querySelector('input[type="number"]');
                            if (stockInput) {
                                stockInput.value = stockQuantity;
                                console.log(`✅ مخزون متهيئ للون مخصص موجود ${colorName}: ${stockQuantity}`);
                                foundInDOM = true;
                            }
                        }
                    });

                    // التأكد الإضافي: البحث أيضًا في حاوية الألوان المخصصة
                    if (!foundInDOM) {
                        const customColorsContainer = document.getElementById('customColorsContainer');
                        if (customColorsContainer) {
                            const existingColorNames = Array.from(customColorsContainer.querySelectorAll('.custom-color-item span'))
                                .map(span => span.textContent);

                            if (existingColorNames.includes(colorName)) {
                                console.log(`⚠️ اللون ${colorName} تم العثور عليه في الحاوية، تسجيل على أنه موجود`);
                                foundInDOM = true;
                            }
                        }
                    }

                    // إنشاء اللون المخصص فقط إذا لم يوجد في أي مكان
                    if (!foundInDOM) {
                        if (colorExistsInProduct) {
                            console.log(`🔍 إنشاء اللون المخصص ${colorName} من بيانات المنتج`);
                            createCustomColorFromProductData(existingColorData, stockQuantity);
                        } else if (stockQuantity > 0) {
                            console.log(`🔍 إنشاء اللون المخصص ${colorName} مع مخزون ${stockQuantity}`);
                            createCustomColorFromStock(colorName, stockQuantity);
                        }
                    } else {
                        console.log(`✅ اللون المخصص ${colorName} موجود بالفعل في الـ DOM، لا يوجد إنشاء`);
                    }
                }
            });

            // تحديث العرض
            updateSelectedColorsCount();
            updateSelectedColorsSummary();
            calculateTotalStock();
        }
    }

    // دالة لإنشاء لون مخصص من بيانات المنتج
    function createCustomColorFromProductData(colorData, stockQuantity) {
        const colorName = colorData.name;
        const hexColor = colorData.hex || generateHexFromName(colorName);

        // إنشاء عنصر HTML للون مخصص
        const customColorHTML = `
            <div class="custom-color-item bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm"
                              style="background-color: ${hexColor}"></span>
                        <span class="text-sm font-medium text-gray-700">${colorName}</span>
                    </div>
                    <button type="button" onclick="removeCustomColor(this)"
                            class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-full transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-xs font-medium text-gray-600">المخزون:</label>
                    <input type="number" name="stock_couleur_custom_${customColorCounter++}"
                           placeholder="0" min="0"
                           class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                           value="${stockQuantity}">
                </div>
                <input type="hidden" name="couleurs_personnalisees[]" value="${colorName}">
            </div>
        `;

        // إضافة للقائمة الخاصة بالألوان المخصصة
        const container = document.getElementById('customColorsContainer');
        if (container) {
            container.insertAdjacentHTML('beforeend', customColorHTML);
            console.log(`✅ اللون المخصص ${colorName} إنشاء من بيانات المنتج مع مخزون ${stockQuantity}`);
        }
    }

    // دالة لإنشاء لون مخصص من بيانات المخزون
    function createCustomColorFromStock(colorName, stockQuantity) {
        // استرجاع اللون السداسي الأصلي بالضبط من بيانات المنتج
        let hexColor = null;

        // البحث عن اللون في بيانات المنتج
        const productColors = @json($product->couleur ?: []);
        if (Array.isArray(productColors)) {
            for (const color of productColors) {
                if (color.name === colorName) {
                    hexColor = color.hex;
                    break;
                }
            }
        }

        // إذا لم يوجد لون، استخدم لونًا مختارًا بشكل افتراضي
        if (!hexColor) {
            hexColor = generateHexFromName(colorName);
        }

        // إنشاء عنصر HTML للون مخصص
        const customColorHTML = `
            <div class="custom-color-item bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200 hover:border-blue-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="w-8 h-8 rounded-full border-2 border-gray-300 shadow-sm"
                              style="background-color: ${hexColor}"></span>
                        <span class="text-sm font-medium text-gray-700">${colorName}</span>
                    </div>
                    <button type="button" onclick="removeCustomColor(this)"
                            class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-full transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-xs font-medium text-gray-600">المخزون:</label>
                    <input type="number" name="stock_couleur_custom_${customColorCounter++}"
                           placeholder="0" min="0"
                           class="w-20 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                           value="${stockQuantity}">
                </div>
                <input type="hidden" name="couleurs_personnalisees[]" value="${colorName}">
            </div>
        `;

        // إضافة للقائمة الخاصة بالألوان المخصصة
        const container = document.getElementById('customColorsContainer');
        if (container) {
            container.insertAdjacentHTML('beforeend', customColorHTML);
            console.log(`✅ اللون المخصص ${colorName} إنشاء مع مخزون ${stockQuantity}`);
        }
    }

    // دالة لإنشاء لون سداسي من اسم
    function generateHexFromName(name) {
        let hash = 0;
        for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
        }

        const hue = Math.abs(hash) % 360;
        const saturation = 70 + (Math.abs(hash) % 30);
        const lightness = 50 + (Math.abs(hash) % 20);

        return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
    }

    // تهيئة المخزون الإجمالي بالقيمة الموجودة مسبقًا للمنتج
    if (stockTotal) stockTotal.value = initialStock;
    if (stockTotalHidden) stockTotalHidden.value = initialStock;

    // حساب المخزون الإجمالي الأولي
    calculateTotalStock();

    // تهيئة المخزونات لكل لون
    initializeStockByColor();


});

// 🆕 دالة لحساب المخزون الإجمالي (مصححة)
function calculateTotalStock() {
    let total = 0;

    console.log('🔄 بدء حساب المخزون الإجمالي...');

    // 1. حساب المخزون للألوان المسبقة فقط (المحددة)
    const predefinedStockInputs = document.querySelectorAll('input[name^="stock_couleur_"]:not([name*="custom"])');
    console.log('📊 المدخلات المخزون للألوان المسبقة تم العثور عليها:', predefinedStockInputs.length);

    predefinedStockInputs.forEach((input, index) => {
        // التأكد من أن اللون محدد
        let colorContainer = input.closest('.color-card');
        let checkbox = colorContainer ? colorContainer.querySelector('input[name="couleurs_predefinies[]"]') : null;

        if (checkbox && checkbox.checked) {
            const value = parseInt(input.value) || 0;
            const colorName = input.getAttribute('data-color-name') || `مسبقة_${index}`;
            total += value;
            console.log(`   ✅ ${colorName}: ${value} وحدة (المجموع: ${total})`);
        } else {
            console.log(`   ⏭️ اللون المسبق غير محدد، تجاهل`);
        }
    });

    // 2. حساب المخزون للألوان المخصصة (الكل)
    const customStockInputs = document.querySelectorAll('input[name^="stock_couleur_custom_"]');
    console.log('📊 المدخلات المخزون للألوان المخصصة تم العثور عليها:', customStockInputs.length);

    customStockInputs.forEach((input, index) => {
        const value = parseInt(input.value) || 0;
        const colorName = input.getAttribute('data-color-name') || `مخصصة_${index}`;
        total += value;
        console.log(`   🎨 ${colorName}: ${value} وحدة (المجموع: ${total})`);
    });

    // 3. تحديث العرض مع التحقق
    const stockTotalElement = document.getElementById('stockTotal');
    const stockTotalHiddenElement = document.getElementById('stockTotalHidden');

    if (stockTotalElement) {
        stockTotalElement.value = total;
        console.log('✅ تم تحديث المخزون الإجمالي المعروض بنجاح:', total);

        // إجبار تحديث العرض
        stockTotalElement.dispatchEvent(new Event('input', { bubbles: true }));
        stockTotalElement.dispatchEvent(new Event('change', { bubbles: true }));

        // تأثير بصري لتحديث العرض
        stockTotalElement.style.backgroundColor = '#d1fae5'; // أخضر رمادي فاتح
        setTimeout(() => {
            stockTotalElement.style.backgroundColor = '#f9fafb'; // إرجاع الطبيعة
        }, 300);
    } else {
        console.warn('⚠️ عنصر stockTotal غير موجود');
    }

    if (stockTotalHiddenElement) {
        stockTotalHiddenElement.value = total;
        console.log('✅ تم تحديث المخزون الإجمالي المخفي بنجاح:', total);
    } else {
        console.warn('⚠️ عنصر stockTotalHidden غير موجود');
    }

    console.log('🎯 المخزون الإجمالي النهائي المحسوب:', total);
    return total;
}

// إعداد البيانات قبل إرسال النموذج
function prepareFormData() {
    console.log('إعداد بيانات النموذج...');

    const selectedPredefinedColors = Array.from(document.querySelectorAll('input[name="couleurs_predefinies[]"]:checked'))
        .map(input => input.value);

    const customColors = Array.from(document.querySelectorAll('#customColorsContainer input[name="couleurs_personnalisees[]"]'))
        .map(input => input.value);

    // دمج جميع الألوان
    const allColors = [...selectedPredefinedColors, ...customColors];

    console.log('الألوان المسبقة محددة:', selectedPredefinedColors);
    console.log('الألوان المخصصة:', customColors);
    console.log('جميع الألوان المدمجة:', allColors);

    // تحديث الألوان المدمجة مرة أخرى قبل إرسال
    updateCombinedColors();

    // التأكد من وجود ألوان على الأقل
    const couleursInputs = document.querySelectorAll('input[name="couleurs[]"]');
    const hasColors = couleursInputs.length > 0;

    console.log('عدد الألوان قبل إرسال:', couleursInputs.length);
    couleursInputs.forEach((input, index) => {
        console.log(`اللون ${index + 1}:`, input.value);
    });

    return hasColors;
}

    // التحقق من النموذج
    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('إرسال النموذج...');

        if (!prepareFormData()) {
            e.preventDefault();
            alert('من فضلك حدد ألوانًا على الأقل');
            return;
        }

        // التأكد من أن حقل الألوان تم إنشاؤه
        const couleursInput = document.querySelector('input[name="couleurs"]');
        if (couleursInput) {
            console.log('حقل الألوان إنشاء مع القيمة:', couleursInput.value);
        } else {
            console.error('حقل الألوان غير موجود!');
        }
    });

    // دالة لتحديث عداد الألوان المحددة
    function updateSelectedColorsCount() {
        const selectedPredefinedColors = document.querySelectorAll('input[name="couleurs_predefinies[]"]:checked');
        const customColors = document.querySelectorAll('#customColorsContainer input[name="couleurs_personnalisees[]"]');
        const totalColors = selectedPredefinedColors.length + customColors.length;

        const countElement = document.getElementById('selectedColorsCount');
        if (countElement) {
            countElement.textContent = `${totalColors} محدد${totalColors > 1 ? 'ة' : ''}`;
        }
    }

    // دالة لعرض ملخص الألوان المحددة
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

        // إضافة الألوان المسبقة المحددة
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
                <span class="text-xs text-green-600">(المخزون: ${stockValue})</span>
            `;

            listElement.appendChild(colorTag);
        });

        // إضافة الألوان المخصصة
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
                <span class="text-xs text-blue-600">(المخزون: ${stockValue})</span>
            `;

            listElement.appendChild(colorTag);
        });
    }

    // تهيئة العرض عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة المخزونات لكل لون
        initializeStockByColor();

        updateSelectedColorsCount();
        updateSelectedColorsSummary();
    });
</script>

<style>
    .color-card {
        transition: all 0.3s ease;
    }
    .color-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* أنماط للألوان في المخزون */
    .color-card.has-stock {
        border-color: #10b981;
        background-color: #f0fdf4;
    }

    /* أنماط للألوان في النقص */
    .color-card.out-of-stock {
        border-color: #ef4444;
        background-color: #fef2f2;
        opacity: 0.8;
    }

    .color-card.out-of-stock .color-name {
        color: #dc2626;
        font-style: italic;
    }

    .color-card.out-of-stock input[type="number"] {
        background-color: #fee2e2;
        border-color: #fca5a5;
    }
</style>
@endsection


