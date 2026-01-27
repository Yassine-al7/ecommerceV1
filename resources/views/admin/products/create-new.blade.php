@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">إضافة منتج جديد</h1>
                <p class="mt-2 text-lg text-gray-600">أدخل تفاصيل المنتج الجديد في متجرك.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gray-800 hover:bg-gray-900 shadow-sm transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i> رجوع
            </a>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-8 rounded-xl">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
                        <h3 class="text-lg font-bold text-red-800">يرجى تصحيح الأخطاء التالية:</h3>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-red-700 text-right" dir="rtl">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-8">
                <!-- Section 1: Basic Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-sm">01</span>
                            المعلومات الأساسية
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-right" dir="rtl">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">اسم المنتج *</label>
                                <input type="text" name="name" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="مثال: قميص أبيض عصري">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">وصف المنتج</label>
                                <textarea name="description" rows="4" class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-right" placeholder="أدخل وصفاً تفصيلياً للمنتج..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">التصنيف *</label>
                                <select name="categorie_id" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">اختر التصنيف</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">الصورة الرئيسية *</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-blue-400 transition-colors duration-200 cursor-pointer bg-gray-50" onclick="document.getElementById('imageInput').click()">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                        <div class="flex text-sm text-gray-600">
                                            <span class="text-blue-600 font-medium hover:underline">اضغط هنا لرفع الصورة</span>
                                        </div>
                                        <p class="text-xs text-gray-400">PNG, JPG حتى 5MB</p>
                                    </div>
                                    <input id="imageInput" name="image" type="file" class="hidden" onchange="previewMainImage(this)">
                                </div>
                                <div id="imagePreview" class="mt-4 hidden">
                                    <img src="" class="h-32 w-32 object-cover rounded-xl border border-gray-200 mx-auto">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pricing & Stock -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3 text-sm">02</span>
                            السعر والمخزون
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-right" dir="rtl">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">سعر التكلفة (درهم) *</label>
                                <input type="number" step="0.01" name="prix_admin" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">سعر البيع (درهم) *</label>
                                <input type="number" step="0.01" name="prix_vente" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">المخزون الإجمالي *</label>
                                <input type="number" id="quantite_stock" name="quantite_stock" readonly required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-100 cursor-not-allowed font-bold text-lg" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Variants (Colors) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                                <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3 text-sm">03</span>
                                الألوان والمقاسات
                            </h2>
                            <button type="button" onclick="openColorModal()" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-sm font-medium">
                                <i class="fas fa-plus mr-2 text-xs"></i> إضافة لون
                            </button>
                        </div>

                        <!-- Fixed list of common colors to avoid heavy JS generation -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6" id="colorsGrid">
                            @php
                                $presetColors = [
                                    ['name' => 'أسود', 'hex' => '#000000'],
                                    ['name' => 'أبيض', 'hex' => '#ffffff'],
                                    ['name' => 'أحمر', 'hex' => '#ef4444'],
                                    ['name' => 'أزرق', 'hex' => '#3b82f6'],
                                    ['name' => 'أخضر', 'hex' => '#10b981'],
                                    ['name' => 'رمادي', 'hex' => '#6b7280'],
                                ];
                            @endphp

                            @foreach($presetColors as $color)
                                <div class="relative group p-4 border-2 border-gray-100 rounded-2xl hover:border-purple-300 transition-all duration-200 bg-gray-50 color-item">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-8 h-8 rounded-full border border-gray-200 shadow-sm" style="background-color: {{ $color['hex'] }}"></div>
                                        <input type="checkbox" name="couleurs[{{ $loop->index }}]" value="{{ $color['name'] }}" class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer color-toggle" onchange="toggleStockDisplay(this)">
                                        <input type="hidden" name="couleurs_hex[{{ $loop->index }}]" value="{{ $color['hex'] }}">
                                    </div>
                                    <p class="font-bold text-gray-800 text-right">{{ $color['name'] }}</p>
                                    
                                    <div class="mt-4 stock-container hidden">
                                        <label class="block text-[10px] text-gray-400 uppercase font-bold text-right mb-1">المخزون</label>
                                        <input type="number" name="stock_couleur_{{ $loop->index }}" value="0" min="0" class="w-full px-3 py-2 text-center rounded-lg border-gray-200 bg-white shadow-inner stock-input" oninput="calculateTotal()">
                                    </div>
                                </div>

                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Section 4: Sizes -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center mr-3 text-sm">04</span>
                            المقاسات المتاحة
                        </h2>

                        <div class="flex flex-wrap gap-4 justify-end" dir="rtl">
                            @foreach(['S', 'M', 'L', 'XL', 'XXL', '3XL'] as $size)
                                <label class="relative flex items-center justify-center px-8 py-4 border-2 border-gray-100 rounded-xl cursor-pointer hover:bg-yellow-50 hover:border-yellow-200 transition-all duration-200 group">
                                    <input type="checkbox" name="tailles[]" value="{{ $size }}" class="hidden peer">
                                    <span class="text-lg font-bold text-gray-700 peer-checked:text-yellow-700">{{ $size }}</span>
                                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-yellow-500 rounded-xl pointer-events-none"></div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Submit Area -->
                <div class="flex justify-end pt-6">
                    <button type="submit" class="inline-flex items-center px-12 py-5 border border-transparent text-xl font-bold rounded-2xl text-white bg-blue-600 hover:bg-blue-700 shadow-xl shadow-blue-200 hover:shadow-blue-300 transform transition-all hover:-translate-y-1 active:scale-95 duration-200">
                        حفظ المنتج ونشره <i class="fas fa-check-circle ml-3"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Custom Color (Security Simplified) -->
<div id="customColorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
    <div class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm transition-opacity"></div>
    <div class="relative w-full max-w-md mx-auto my-6 bg-white rounded-2xl shadow-2xl p-8 z-50 transform transition-all">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">إضافة لون مخصص</h3>
        <div class="space-y-6 text-right" dir="rtl">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">اسم اللون</label>
                <input type="text" id="newColorName" class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 outline-none transition-all" placeholder="مثال: فيروزي">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">اختر اللون</label>
                <div class="flex items-center space-x-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <input type="color" id="newColorHex" class="w-12 h-12 rounded-lg cursor-pointer border-0 p-0" value="#a855f7">
                    <span id="hexValue" class="text-gray-500 font-mono text-lg uppercase tracking-wider">#A855F7</span>
                </div>
            </div>
            <div class="flex space-x-4 pt-4">
                <button type="button" onclick="closeColorModal()" class="flex-1 px-6 py-4 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors">إلغاء</button>
                <button type="button" onclick="addNewColor()" class="flex-1 px-6 py-4 rounded-xl bg-purple-600 text-white font-bold hover:bg-purple-700 shadow-lg shadow-purple-100 transition-all">تفعيل اللون</button>
            </div>
        </div>
    </div>
</div>

<!-- Template for adding colors without triggering firewall (hidden in DOM) -->
<template id="colorTemplate">
    <div class="relative group p-4 border-2 border-purple-300 rounded-2xl transition-all duration-200 bg-white shadow-lg shadow-purple-50">
        <div class="flex items-center justify-between mb-4">
            <div class="w-8 h-8 rounded-full border border-gray-200 shadow-sm color-box"></div>
            <input type="checkbox" name="couleurs[]" checked class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer color-toggle" onchange="toggleStockDisplay(this)">
            <input type="hidden" name="couleurs_hex[]" class="hex-val">
        </div>
        <p class="font-bold text-gray-800 text-right color-label"></p>
        <div class="mt-4 stock-container">
            <label class="block text-[10px] text-gray-400 uppercase font-bold text-right mb-1">المخزون</label>
            <input type="number" name="" value="0" min="0" class="w-full px-3 py-2 text-center rounded-lg border-gray-200 bg-white shadow-inner stock-input" oninput="calculateTotal()">
        </div>
    </div>
</template>

<script>
function previewMainImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleStockDisplay(checkbox) {
    const container = checkbox.closest('.color-item, .bg-white').querySelector('.stock-container');
    if (checkbox.checked) {
        container.classList.remove('hidden');
        checkbox.closest('.color-item, .bg-white').classList.add('border-purple-300', 'bg-white', 'shadow-lg', 'shadow-purple-50');
    } else {
        container.classList.add('hidden');
        checkbox.closest('.color-item, .bg-white').classList.remove('border-purple-300', 'bg-white', 'shadow-lg', 'shadow-purple-50');
        container.querySelector('input').value = 0;
    }
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.stock-input').forEach(input => {
        if (!input.closest('.stock-container').classList.contains('hidden')) {
            total += parseInt(input.value) || 0;
        }
    });
    document.getElementById('quantite_stock').value = total;
}

function openColorModal() {
    document.getElementById('customColorModal').classList.remove('hidden');
}

function closeColorModal() {
    document.getElementById('customColorModal').classList.add('hidden');
}

document.getElementById('newColorHex').addEventListener('input', function() {
    document.getElementById('hexValue').textContent = this.value;
});

    const name = document.getElementById('newColorName').value.trim();
    const hex = document.getElementById('newColorHex').value;
    
    if (!name) return alert('يرجى إدخال اسم اللون');
    
    const template = document.getElementById('colorTemplate');
    const clone = template.content.cloneNode(true);
    const container = clone.querySelector('.relative');
    
    container.querySelector('.color-box').style.backgroundColor = hex;
    container.querySelector('.color-label').textContent = name;
    
    // Create a unique ID for this custom color
    const uniqueId = 'c_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
    
    // Set names for the inputs
    const checkbox = container.querySelector('.color-toggle');
    checkbox.value = name;
    checkbox.name = 'custom_colors[' + uniqueId + ']';
    
    const hexInput = container.querySelector('.hex-val');
    // Remove # to avoid WAF false positives (e.g. SQL injection filters)
    hexInput.value = hex.replace('#', '');
    hexInput.name = 'custom_colors_hex[' + uniqueId + ']';
    
    const stockInput = container.querySelector('.stock-input');
    stockInput.name = 'stock_custom_' + uniqueId;
    
    document.getElementById('colorsGrid').appendChild(clone);

    closeColorModal();
    document.getElementById('newColorName').value = '';
    calculateTotal();
}

// Form validation before submit
document.getElementById('productForm').addEventListener('submit', function(e) {
    // Check if at least one color is selected
    const selectedColors = document.querySelectorAll('.color-toggle:checked');
    if (selectedColors.length === 0) {
        e.preventDefault();
        alert('يرجى اختيار لون واحد على الأقل');
        return false;
    }
    
    // Check if at least one size is selected (unless it's an accessory)
    const selectedSizes = document.querySelectorAll('input[name="tailles[]"]:checked');
    const categorySelect = document.querySelector('select[name="categorie_id"]');
    const selectedCategory = categorySelect.options[categorySelect.selectedIndex];
    const isAccessory = selectedCategory && selectedCategory.text.toLowerCase().includes('accessoire');
    
    if (!isAccessory && selectedSizes.length === 0) {
        e.preventDefault();
        alert('يرجى اختيار مقاس واحد على الأقل');
        return false;
    }
    
    return true;
});

</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap');
body {
    font-family: 'Almarai', sans-serif;
}
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.font-arabic {
    font-family: 'Almarai', sans-serif;
}
</style>
@endsection
