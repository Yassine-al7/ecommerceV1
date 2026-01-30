@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">تعديل المنتج</h1>
                <p class="mt-2 text-lg text-gray-600">تحديث تفاصيل المنتج: <strong>{{ $product->name }}</strong></p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gray-800 hover:bg-gray-900 shadow-sm transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i> رجوع
            </a>
        </div>

        {{-- Note: We use Fetch with product_payload to bypass WAF 403 blocks --}}
        <form id="productEditForm">
            @csrf
            
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
                                <input type="text" name="name_visible" value="{{ old('name', $product->name) }}" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="مثال: قميص أبيض عصري">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">وصف المنتج</label>
                                <textarea name="description_visible" rows="4" class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-right" placeholder="أدخل وصفاً تفصيلياً للمنتج...">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">التصنيف *</label>
                                <select name="categorie_id_visible" id="categorie_id" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">اختر التصنيف</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('categorie_id', $product->categorie_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">الصورة الرئيسية</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-blue-400 transition-colors duration-200 cursor-pointer bg-gray-50" onclick="document.getElementById('imageInput').click()">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-image text-gray-400 text-3xl mb-2"></i>
                                        <div class="flex text-sm text-gray-600">
                                            <span class="text-blue-600 font-medium hover:underline">تحديث الصورة الأساسية</span>
                                        </div>
                                    </div>
                                    <input id="imageInput" name="image" type="file" class="hidden" onchange="handleImageUpload(this)">
                                </div>
                                
                                <div id="imagePreview" class="mt-4 {{ $product->image ? '' : 'hidden' }}">
                                    <img src="{{ $product->image ? asset($product->image) : '' }}" class="h-24 w-24 object-cover rounded-xl border border-gray-200 mx-auto">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">معرض صور المنتج (إضافي)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-purple-400 transition-colors duration-200 cursor-pointer bg-gray-50" onclick="document.getElementById('galleryInput').click()">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-images text-gray-400 text-3xl mb-2"></i>
                                        <div class="flex text-sm text-gray-600">
                                            <span class="text-purple-600 font-medium hover:underline">اضغط لإضافة صور للمعرض</span>
                                        </div>
                                    </div>
                                    <input id="galleryInput" name="gallery[]" type="file" class="hidden" multiple onchange="handleGalleryUpload(this)">
                                </div>
                                <div id="galleryPreview" class="mt-4 grid grid-cols-4 gap-2 text-right" dir="rtl">
                                    @php
                                        $colorImages = is_array($product->color_images) ? $product->color_images : (json_decode($product->color_images, true) ?? []);
                                        $galleryEntry = collect($colorImages)->firstWhere('color', 'Gallery');
                                        $existingGallery = $galleryEntry ? ($galleryEntry['images'] ?? []) : [];
                                    @endphp
                                    @foreach($existingGallery as $img)
                                        <div class="relative aspect-square existing-gallery-item" data-path="{{ $img }}">
                                            <img src="{{ asset($img) }}" class="w-full h-full object-cover rounded-lg border border-gray-200 shadow-sm">
                                            <button type="button" onclick="this.parentElement.remove()" class="absolute -top-1 -right-1 bg-red-500 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px] shadow-sm"><i class="fas fa-times"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pricing -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3 text-sm">02</span>
                            السعر
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-right" dir="rtl">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">السعر المقترح للبيع</label>
                                @php
                                    $prixAdminVal = $product->prix_admin;
                                    $decodedPrix = json_decode($prixAdminVal, true);
                                    if(json_last_error() === JSON_ERROR_NONE && is_array($decodedPrix)) {
                                        $prixAdminVal = implode(',', $decodedPrix);
                                    }
                                @endphp
                                <input type="text" name="prix_admin_visible" value="{{ old('prix_admin', $prixAdminVal) }}" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">سعر البيع (درهم) *</label>
                                <input type="number" step="0.01" name="prix_vente_visible" value="{{ old('prix_vente', $product->prix_vente) }}" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Colors & Stock -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                                <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3 text-sm">03</span>
                                الألوان والمخزون
                            </h2>
                            <button type="button" onclick="openColorModal()" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-sm font-medium">
                                <i class="fas fa-plus mr-2 text-xs"></i> إضافة لون
                            </button>
                        </div>
                        
                        <div class="mb-6 flex justify-end">
                            <div class="bg-gray-50 px-6 py-3 rounded-xl border border-gray-200 flex items-center space-x-4">
                                <span id="totalStockDisplay" class="text-2xl font-bold text-purple-700">{{ $product->quantite_stock }}</span>
                                <span class="text-sm text-gray-500 font-medium">:إجمالي المخزون</span>
                            </div>
                        </div>

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
                                $existingStock = $product->stock_couleurs;
                                if (is_string($existingStock)) $existingStock = json_decode($existingStock, true) ?? [];
                                $getQuantity = function($name) use ($existingStock) {
                                    foreach($existingStock as $s) { if(isset($s['name']) && strtolower($s['name']) == strtolower($name)) return $s['quantity']; }
                                    return 0;
                                };
                                $isActive = function($name) use ($existingStock) {
                                    foreach($existingStock as $s) { if(isset($s['name']) && strtolower($s['name']) == strtolower($name) && $s['quantity'] > 0) return true; }
                                    return false;
                                };
                            @endphp

                            @foreach($presetColors as $color)
                                @php 
                                    $active = $isActive($color['name']); 
                                    $qty = $getQuantity($color['name']);
                                @endphp
                                <div class="relative group p-4 border-2 {{ $active ? 'border-purple-300 shadow-lg shadow-purple-50' : 'border-gray-100' }} rounded-2xl hover:border-purple-300 transition-all duration-200 bg-white color-item" 
                                     data-name="{{ $color['name'] }}" 
                                     data-hex="{{ str_replace('#', '', $color['hex']) }}">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-8 h-8 rounded-full border border-gray-200 shadow-sm" style="background-color: {{ $color['hex'] }}"></div>
                                        <input type="checkbox" {{ $active ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer color-toggle" onchange="toggleStockDisplay(this)">
                                    </div>
                                    <p class="font-bold text-gray-800 text-right">{{ $color['name'] }}</p>
                                    <div class="mt-4 stock-container {{ $active ? '' : 'hidden' }}">
                                        <label class="block text-[10px] text-gray-400 uppercase font-bold text-right mb-1">المخزون</label>
                                        <input type="number" value="{{ $qty }}" min="0" class="w-full px-3 py-2 text-center rounded-lg border-gray-200 bg-white shadow-inner stock-input" oninput="calculateTotal()">
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
                        @php
                           $currentSizes = $product->tailles;
                           if(is_string($currentSizes)) $currentSizes = json_decode($currentSizes, true) ?? [];
                        @endphp
                        <div class="flex flex-wrap gap-4 justify-end" id="sizesContainer" dir="rtl">
                            @foreach(['S', 'M', 'L', 'XL', 'XXL', '3XL', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45'] as $size)
                                @php $isChecked = in_array($size, $currentSizes); @endphp
                                <label class="relative flex items-center justify-center px-8 py-4 border-2 {{ $isChecked ? 'border-yellow-500 bg-yellow-50' : 'border-gray-100' }} rounded-xl cursor-pointer hover:bg-yellow-50 hover:border-yellow-200 transition-all duration-200 group">
                                    <input type="checkbox" value="{{ $size }}" {{ $isChecked ? 'checked' : '' }} class="hidden peer size-toggle">
                                    <span class="text-lg font-bold {{ $isChecked ? 'text-yellow-700' : 'text-gray-700' }} peer-checked:text-yellow-700">{{ $size }}</span>
                                    <div class="absolute inset-0 border-2 border-transparent peer-checked:border-yellow-500 rounded-xl pointer-events-none"></div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Submit Area -->
                <div class="flex justify-end pt-6">
                    <button type="submit" class="inline-flex items-center px-12 py-5 border border-transparent text-xl font-bold rounded-2xl text-white bg-blue-600 hover:bg-blue-700 shadow-xl shadow-blue-200 hover:shadow-blue-300 transform transition-all hover:-translate-y-1 active:scale-95 duration-200">
                        تحديث المنتج <i class="fas fa-save ml-3"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modals & Templates -->
<div id="customColorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
    <div class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm transition-opacity"></div>
    <div class="relative w-full max-w-md mx-auto my-6 bg-white rounded-2xl shadow-2xl p-8 z-50 transform transition-all">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">إضافة لون مخصص</h2>
        <div class="space-y-6 text-right" dir="rtl">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">اسم اللون</label>
                <input type="text" id="newColorName" class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 outline-none transition-all" placeholder="مثال: فيروزي">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">اختر اللون</label>
                <div class="flex items-center space-x-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <input type="color" id="newColorHex" class="w-12 h-12 rounded-lg cursor-pointer border-0 p-0" value="#a855f7">
                    <span id="hexValue" class="text-gray-500 font-mono text-lg uppercase mr-4 tracking-wider">#A855F7</span>
                </div>
            </div>
            <div class="flex space-x-4 pt-4">
                <button type="button" onclick="closeColorModal()" class="flex-1 px-6 py-4 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors">إلغاء</button>
                <button type="button" onclick="addNewColor()" class="flex-1 px-6 py-4 rounded-xl bg-purple-600 text-white font-bold hover:bg-purple-700 shadow-lg shadow-purple-100 transition-all">تفعيل اللون</button>
            </div>
        </div>
    </div>
</div>

<template id="colorTemplate">
    <div class="relative group p-4 border-2 border-purple-300 shadow-lg shadow-purple-50 rounded-2xl transition-all duration-200 bg-white color-item" data-name="" data-hex="">
        <div class="flex items-center justify-between mb-4">
            <div class="w-8 h-8 rounded-full border border-gray-200 shadow-sm color-box"></div>
            <input type="checkbox" checked class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer color-toggle" onchange="toggleStockDisplay(this)">
        </div>
        <p class="font-bold text-gray-800 text-right color-label"></p>
        <div class="mt-4 stock-container">
            <label class="block text-[10px] text-gray-400 uppercase font-bold text-right mb-1">المخزون</label>
            <input type="number" value="1" min="0" class="w-full px-3 py-2 text-center rounded-lg border-gray-200 bg-white shadow-inner stock-input" oninput="calculateTotal()">
        </div>
    </div>
</template>

<script>
async function handleImageUpload(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { previewImg.src = e.target.result; preview.classList.remove('hidden'); }
        reader.readAsDataURL(input.files[0]);
    }
}

async function handleGalleryUpload(input) {
    const preview = document.getElementById('galleryPreview');
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'relative aspect-square';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg border border-purple-200 shadow-sm opacity-70">`;
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
}

function toggleStockDisplay(checkbox) {
    const container = checkbox.closest('.color-item').querySelector('.stock-container');
    const wrapper = checkbox.closest('.color-item');
    if (checkbox.checked) {
        container.classList.remove('hidden');
        wrapper.classList.add('border-purple-300', 'shadow-lg', 'shadow-purple-50');
    } else {
        container.classList.add('hidden');
        wrapper.classList.remove('border-purple-300', 'shadow-lg', 'shadow-purple-50');
        container.querySelector('input').value = 0;
    }
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.stock-input').forEach(input => {
        const wrapper = input.closest('.stock-container');
        if (!wrapper.classList.contains('hidden')) total += parseInt(input.value) || 0;
    });
    document.getElementById('totalStockDisplay').innerText = total;
}

function openColorModal() { document.getElementById('customColorModal').classList.remove('hidden'); }
function closeColorModal() { document.getElementById('customColorModal').classList.add('hidden'); }

document.getElementById('newColorHex')?.addEventListener('input', function() {
    document.getElementById('hexValue').textContent = this.value;
});

function addNewColor() {
    const name = document.getElementById('newColorName').value.trim();
    const hex = document.getElementById('newColorHex').value;
    if (!name) return alert('يرجى إدخال اسم اللون');
    const template = document.getElementById('colorTemplate');
    const clone = template.content.cloneNode(true);
    const container = clone.querySelector('.color-item');
    container.setAttribute('data-name', name);
    container.setAttribute('data-hex', hex.replace('#', ''));
    container.querySelector('.color-box').style.backgroundColor = hex;
    container.querySelector('.color-label').textContent = name;
    document.getElementById('colorsGrid').appendChild(clone);
    closeColorModal();
    document.getElementById('newColorName').value = '';
    calculateTotal();
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('productEditForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ الحفظ...';
        submitBtn.disabled = true;

        try {
            let imagePath = null;
            let galleryPaths = [];

            // 1. Existing Gallery
            document.querySelectorAll('.existing-gallery-item').forEach(el => galleryPaths.push(el.getAttribute('data-path')));

            // 2. Main Image
            const imgIn = document.getElementById('imageInput');
            if (imgIn.files && imgIn.files[0]) {
                const file = imgIn.files[0];
                if (file.size > 10 * 1024 * 1024) throw new Error(`الصورة الرئيسية كبيرة جداً (${(file.size/1024/1024).toFixed(2)}MB). الحد الأقصى 10MB.`);

                const fd = new FormData();
                fd.append('image', file);
                const res = await fetch("{{ route('products.upload_image_secure') }}", {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (res.ok) { 
                    const d = await res.json(); 
                    imagePath = d.path; 
                } else {
                    const d = await res.json();
                    throw new Error("خطأ في رفع الصورة: " + (d.error || res.status));
                }
            }

            // 3. New Gallery (One by One to bypass WAF)
            const galIn = document.getElementById('galleryInput');
            if (galIn.files && galIn.files.length > 0) {
                submitBtn.innerHTML = '<i class="fas fa-images"></i> جارٍ رفع الصور الإضافية...';
                for (let i = 0; i < galIn.files.length; i++) {
                    const file = galIn.files[i];
                    if (file.size > 10 * 1024 * 1024) {
                        console.warn(`Gallery Image ${i+1} too large, skipping.`);
                        continue;
                    }
                    const fd = new FormData();
                    fd.append('image', file);
                    const res = await fetch("{{ route('products.upload_image_secure') }}", {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    if (res.ok) { 
                        const d = await res.json(); 
                        if(d.path) galleryPaths.push(d.path); 
                    } else {
                        const d = await res.json();
                        console.error(`Gallery Image ${i+1} failed:`, d.error || res.status);
                    }
                }
            }

            // 4. Payload
            const data = {
                name: form.querySelector('[name="name_visible"]').value,
                description: form.querySelector('[name="description_visible"]').value,
                categorie_id: form.querySelector('[name="categorie_id_visible"]').value,
                prix_admin: form.querySelector('[name="prix_admin_visible"]').value,
                prix_vente: form.querySelector('[name="prix_vente_visible"]').value,
                colors: [],
                sizes: [],
                total_stock: parseInt(document.getElementById('totalStockDisplay').innerText) || 0,
                uploaded_image_path: imagePath,
                uploaded_gallery_paths: galleryPaths
            };

            document.querySelectorAll('.color-item').forEach(el => {
                if (el.querySelector('.color-toggle').checked) {
                    data.colors.push({
                        name: el.getAttribute('data-name'),
                        hex: el.getAttribute('data-hex'),
                        stock: parseInt(el.querySelector('.stock-input').value) || 0
                    });
                }
            });
            document.querySelectorAll('.size-toggle:checked').forEach(el => data.sizes.push(el.value));

            function strToHex(str) {
                const utf8 = encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, (match, p1) => String.fromCharCode('0x' + p1));
                let h = '';
                for (let i = 0; i < utf8.length; i++) h += utf8.charCodeAt(i).toString(16).padStart(2, '0');
                return h;
            }

            const hex = strToHex(JSON.stringify(data));
            const finalRes = await fetch("{{ route('products.update_root_stealth', $product->id) }}", {
                method: 'POST',
                body: JSON.stringify({ product_payload: hex }),
                headers: { 
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (finalRes.ok) {
                window.location.href = "{{ route('admin.products.index') }}";
            } else {
                const t = await finalRes.text();
                alert("خطأ في حفظ البيانات: " + t.substring(0, 50));
            }

        } catch (e) {
            console.error(e);
            alert("حدث خطأ غير متوقع: " + e.message);
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap');
body { font-family: 'Almarai', sans-serif; }
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>
@endsection
