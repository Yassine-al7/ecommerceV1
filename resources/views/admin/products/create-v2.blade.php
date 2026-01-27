@extends('layouts.app')

@section('header_styles')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Cairo', sans-serif; }
    .form-glass {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .input-premium {
        @apply w-full px-4 py-3 rounded-xl border-gray-200 transition-all duration-300 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 bg-gray-50/50;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    .color-dot {
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 9999px;
        transition: transform 0.2s;
    }
    .color-card-v2 {
        @apply border-2 border-transparent rounded-2xl p-4 transition-all duration-300 bg-white shadow-sm hover:shadow-md cursor-pointer flex items-center justify-between;
    }
    .color-card-v2.active {
        @apply border-blue-500 bg-blue-50/30 shadow-none;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50/50 py-12 px-4 sm:px-6 lg:px-8" dir="rtl">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-gray-400 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">لوحة التحكم</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.products.index') }}" class="hover:text-blue-600 transition-colors">المنتجات</a>
            <span class="mx-2">/</span>
            <span class="text-blue-600">إضافة منتج جديد</span>
        </nav>

        <!-- Header -->
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">منتج جديد</h1>
                <p class="mt-2 text-lg text-gray-500">أضف تفاصيل المنتج والصور والألوان المتاحة.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="flex items-center text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-times ml-2 bg-white p-2 rounded-full shadow-sm"></i>
                إلغاء
            </a>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="v2Form">
            @csrf

            <!-- Section 1: Basic Info -->
            <div class="form-glass rounded-3xl p-8 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم المنتج <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="input-premium text-lg" placeholder="مثال: قميص أزرق عالي الجودة" value="{{ old('name') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">التصنيف <span class="text-red-500">*</span></label>
                        <select name="categorie_id" required class="input-premium appearance-none">
                            <option value="">اختر التصنيف</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('categorie_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">سعر البيع (درهم) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="prix_vente" required class="input-premium" placeholder="0.00" value="{{ old('prix_vente') }}">
                    </div>
                </div>
            </div>

            <!-- Section 2: Colors & Stock -->
            <div class="form-glass rounded-3xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">الألوان والمخزون</h3>
                    <button type="button" onclick="showColorModal()" class="text-blue-600 hover:text-blue-700 text-sm font-bold flex items-center">
                        <i class="fas fa-plus-circle ml-1"></i>
                        أضف لون خاص
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="colorContainer">
                    @php
                        $predefinedColors = [
                            'أسود' => '#000000',
                            'أبيض' => '#FFFFFF',
                            'أحمر' => '#EF4444',
                            'أزرق' => '#3B82F6',
                            'أخضر' => '#22C55E',
                            'رمادي' => '#6B7280'
                        ];
                    @endphp

                    @foreach($predefinedColors as $name => $hex)
                        <div class="color-card-v2" onclick="toggleColor(this, '{{ $loop->index }}')">
                            <div class="flex items-center gap-4">
                                <span class="color-dot border border-gray-100 shadow-sm" style="background-color: {{ $hex }}"></span>
                                <span class="font-bold text-gray-700">{{ $name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="hidden stock-input-wrapper flex items-center bg-gray-100 rounded-lg px-2 py-1">
                                    <span class="text-[10px] text-gray-400 ml-1">المخزون:</span>
                                    <input type="number" name="stock_couleur_{{ $loop->index }}" value="0" min="0" 
                                           class="w-12 bg-transparent text-center border-none focus:ring-0 text-sm p-0"
                                           onclick="event.stopPropagation()">
                                </div>
                                <input type="checkbox" name="couleurs[{{ $loop->index }}]" value="{{ $name }}" class="hidden color-checkbox-v2">
                                <input type="hidden" name="couleurs_hex[{{ $loop->index }}]" value="{{ $hex }}">
                                <i class="fas fa-check-circle text-blue-500 opacity-0 transition-opacity check-icon"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Section 3: Media -->
            <div class="form-glass rounded-3xl p-8 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-6">صورة المنتج</h3>
                <div class="relative group">
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center transition-colors group-hover:border-blue-400">
                        <input type="file" name="image" id="main_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                        <div id="preview-container" class="hidden flex justify-center mb-4">
                            <img id="image-preview" src="#" alt="Preview" class="h-48 w-48 object-cover rounded-2xl shadow-lg border-4 border-white">
                        </div>
                        <div id="upload-placeholder" class="space-y-2">
                            <i class="fas fa-cloud-upload-alt text-4xl text-blue-500"></i>
                            <p class="text-gray-600 font-bold">اسحب الصورة هنا أو انقر للاختيار</p>
                            <p class="text-gray-400 text-sm font-medium">PNG, JPG حتى 5MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing for Admin/Seller -->
            <div class="form-glass rounded-3xl p-8 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-6 font-arabic">إعدادات البيع والعمولة</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">ثمن المنتج عند الأدمن (درهم)</label>
                        <input type="text" name="prix_admin" required class="input-premium" placeholder="100, 150, 200..." value="{{ old('prix_admin') }}">
                        <p class="mt-1 text-xs text-gray-400">يمكنك إدخال عدة أثمان مفصولة بفواصل.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">المخزون الإجمالي المتاح</label>
                        <input type="number" name="quantite_stock" id="total_stock" readonly class="input-premium bg-gray-50 text-blue-600 font-bold" value="0">
                        <p class="mt-1 text-xs text-gray-400">يتم احتسابه تلقائياً من مخزون الألوان.</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 btn-gradient text-white font-bold py-5 rounded-2xl shadow-xl shadow-blue-500/20 hover:scale-[1.02] transition-transform active:scale-95">
                    حفظ المنتج في المتجر
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Simple Hidden template for colors to avoid 403 blocks -->
<template id="customColorTpl">
    <div class="color-card-v2 active">
        <div class="flex items-center gap-4">
            <span class="color-dot border border-gray-100 shadow-sm" id="tpl-dot"></span>
            <span class="font-bold text-gray-700" id="tpl-name"></span>
        </div>
        <div class="flex items-center gap-3">
            <div class="stock-input-wrapper flex items-center bg-gray-100 rounded-lg px-2 py-1">
                <span class="text-[10px] text-gray-400 ml-1 text-center">المخزون:</span>
                <input type="number" name="STOCK_NAME" value="0" min="0" class="w-12 bg-transparent text-center border-none focus:ring-0 text-sm p-0">
            </div>
            <input type="checkbox" name="CHECK_NAME" value="VAL" checked class="hidden">
            <input type="hidden" name="HEX_NAME" value="VAL">
            <i class="fas fa-check-circle text-blue-500 check-icon"></i>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script>
    function toggleColor(card, id) {
        const checkbox = card.querySelector('.color-checkbox-v2');
        const icon = card.querySelector('.check-icon');
        const stock = card.querySelector('.stock-input-wrapper');
        
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            card.classList.add('active');
            icon.classList.remove('opacity-0');
            stock.classList.remove('hidden');
            stock.classList.add('flex');
        } else {
            card.classList.remove('active');
            icon.classList.add('opacity-0');
            stock.classList.add('hidden');
            stock.classList.remove('flex');
        }
        updateTotalStock();
    }

    function updateTotalStock() {
        let total = 0;
        document.querySelectorAll('input[type="number"][name*="stock_couleur"]').forEach(input => {
            const card = input.closest('.color-card-v2');
            if (card.classList.contains('active')) {
                total += parseInt(input.value) || 0;
            }
        });
        document.getElementById('total_stock').value = total;
    }

    document.addEventListener('input', (e) => {
        if (e.target.name && e.target.name.startsWith('stock_couleur')) {
            updateTotalStock();
        }
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('preview-container').classList.remove('hidden');
                document.getElementById('upload-placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function showColorModal() {
        // Minimal modal to avoid large JS blocks
        const name = prompt("أدخل اسم اللون الجديد:");
        if (!name) return;
        const hex = prompt("أدخل كود اللون (مثل #FF0000):", "#000000");
        if (!hex) return;
        
        const id = Date.now();
        const tpl = document.getElementById('customColorTpl');
        const clone = tpl.content.cloneNode(true);
        
        clone.querySelector('#tpl-dot').style.backgroundColor = hex;
        clone.querySelector('#tpl-name').textContent = name;
        clone.querySelector('input[name="STOCK_NAME"]').name = "stock_couleur_" + id;
        clone.querySelector('input[name="CHECK_NAME"]').name = "couleurs[" + id + "]";
        clone.querySelector('input[name="CHECK_NAME"]').value = name;
        clone.querySelector('input[name="HEX_NAME"]').name = "couleurs_hex[" + id + "]";
        clone.querySelector('input[name="HEX_NAME"]').value = hex;
        
        document.getElementById('colorContainer').appendChild(clone);
        updateTotalStock();
    }
</script>
@endsection
