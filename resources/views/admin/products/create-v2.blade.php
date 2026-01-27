@extends('layouts.app')

@section('header_styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary: #4F46E5;
        --secondary: #6366F1;
        --bg-main: #F8FAFC;
        --card-bg: #FFFFFF;
        --text-dark: #1E293B;
        --text-light: #64748B;
        --border-color: #E2E8F0;
    }

    body {
        font-family: 'Plus Jakarta Sans', 'Cairo', sans-serif;
        background-color: var(--bg-main);
        color: var(--text-dark);
        margin: 0;
        padding: 0;
    }

    .form-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .header-section {
        margin-bottom: 32px;
        text-align: right;
    }

    .header-section h1 {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 8px;
        background: linear-gradient(to right, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .header-section p {
        color: var(--text-light);
        font-size: 16px;
    }

    .card-v3 {
        background: var(--card-bg);
        border-radius: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        padding: 32px;
        margin-bottom: 24px;
        border: 1px solid var(--border-color);
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--text-dark);
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 12px;
    }

    .section-title i {
        color: var(--primary);
        font-size: 20px;
    }

    .input-group {
        margin-bottom: 24px;
    }

    .label-v3 {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
        text-align: right;
    }

    .input-v3 {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background-color: #F8FAFC;
        font-size: 15px;
        transition: all 0.2s ease;
        outline: none;
        text-align: right;
        box-sizing: border-box;
    }

    .input-v3:focus {
        border-color: var(--primary);
        background-color: #FFFFFF;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .upload-zone {
        border: 2px dashed var(--border-color);
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #FDFDFF;
        position: relative;
    }

    .upload-zone:hover {
        border-color: var(--primary);
        background: rgba(79, 70, 229, 0.02);
    }

    .upload-zone i {
        font-size: 40px;
        color: var(--primary);
        margin-bottom: 16px;
    }

    .upload-zone p {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-light);
    }

    .swatch-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 16px;
        margin-top: 16px;
    }

    .swatch-card {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #FFFFFF;
        position: relative;
    }

    .swatch-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .swatch-card.active {
        border-color: var(--primary);
        background: rgba(79, 70, 229, 0.04);
        border-width: 2px;
    }

    .swatch-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .swatch-card.active .swatch-circle i {
        color: white;
        text-shadow: 0 0 4px rgba(0,0,0,0.5);
        display: block;
    }

    .swatch-circle i {
        display: none;
    }

    .swatch-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .stock-input-mini {
        width: 60px;
        padding: 4px 8px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        font-size: 12px;
        text-align: center;
        margin-top: 4px;
    }

    .submit-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 16px;
        border: none;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(79, 70, 229, 0.5);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    #image-preview-v3 {
        max-width: 100%;
        max-height: 200px;
        border-radius: 12px;
        margin-top: 16px;
        display: none;
    }

    .total-stock-banner {
        background: rgba(79, 70, 229, 0.05);
        border-radius: 16px;
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        border: 1px solid rgba(79, 70, 229, 0.1);
    }

    .total-stock-banner span {
        font-weight: 700;
        color: var(--primary);
    }
</style>
@endsection

@section('content')
<div class="form-container" dir="rtl">
    <div class="header-section">
        <h1>إضافة منتج جديد</h1>
        <p>قم بملء البيانات أدناه لإنشاء منتج جديد في متجرك.</p>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="v3ProductForm">
        @csrf

        <!-- المعلومات الأساسية -->
        <div class="card-v3">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i>
                المعلومات الأساسية
            </h2>
            
            <div class="input-group">
                <label class="label-v3">اسم المنتج</label>
                <input type="text" name="name" required class="input-v3" placeholder="أدخل اسم المنتج بالكامل" value="{{ old('name') }}">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="input-group">
                    <label class="label-v3">التصنيف</label>
                    <select name="categorie_id" required class="input-v3">
                        <option value="">اختر التصنيف الرئيسي</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('categorie_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label class="label-v3">سعر البيع (درهم)</label>
                    <input type="number" step="0.01" name="prix_vente" required class="input-v3" placeholder="0.00" value="{{ old('prix_vente') }}">
                </div>
            </div>
        </div>

        <!-- الألوان والمخزون -->
        <div class="card-v3">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 class="section-title" style="margin-bottom: 0; border: none; padding: 0;">
                    <i class="fas fa-palette"></i>
                    الألوان والمخزون
                </h2>
                <button type="button" onclick="triggerCustomColor()" style="background: none; border: none; color: var(--primary); font-weight: 600; cursor: pointer; font-size: 14px;">
                    <i class="fas fa-plus-circle"></i> إضافة لون مخصص
                </button>
            </div>

            <div class="swatch-grid" id="colorPickerGrid">
                @php
                    $colors = [
                        ['name' => 'أسود', 'hex' => '#000000'],
                        ['name' => 'أبيض', 'hex' => '#FFFFFF'],
                        ['name' => 'أحمر', 'hex' => '#EF4444'],
                        ['name' => 'أزرق', 'hex' => '#3B82F6'],
                        ['name' => 'أخضر', 'hex' => '#22C55E'],
                        ['name' => 'رمادي', 'hex' => '#94A3B8']
                    ];
                @endphp

                @foreach($colors as $index => $color)
                <div class="swatch-card" onclick="selectSwatch(this, {{ $index }})">
                    <div class="swatch-circle" style="background-color: {{ $color['hex'] }}">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="swatch-name">{{ $color['name'] }}</span>
                    <input type="checkbox" name="couleurs[{{ $index }}]" value="{{ $color['name'] }}" class="hidden-check" style="display: none;">
                    <input type="hidden" name="couleurs_hex[{{ $index }}]" value="{{ $color['hex'] }}">
                    <div class="stock-container" style="display: none;">
                        <input type="number" name="stock_couleur_{{ $index }}" value="0" min="0" class="stock-input-mini" onclick="event.stopPropagation()" oninput="updateTotalStockV3()">
                    </div>
                </div>
                @endforeach
            </div>

            <div class="total-stock-banner">
                <p style="font-size: 14px; font-weight: 600; color: var(--text-light);">إجمالي المخزون المتاح:</p>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span id="totalStockValue">0</span>
                    <input type="hidden" name="quantite_stock" id="total_stock_hidden" value="0">
                </div>
            </div>
        </div>

        <!-- الوسائط -->
        <div class="card-v3">
            <h2 class="section-title">
                <i class="fas fa-image"></i>
                صورة المنتج
            </h2>
            
            <div class="upload-zone" onclick="document.getElementById('fileInputV3').click()">
                <input type="file" name="image" id="fileInputV3" style="display: none;" onchange="handleFileV3(this)">
                <div id="uploadPlaceholderV3">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>انقر هنا لرفع صورة المنتج الرئيسية</p>
                    <p style="margin-top: 8px; font-size: 12px;">JPG, PNG حتى 5 ميجابايت</p>
                </div>
                <img id="image-preview-v3" src="#" alt="Preview">
            </div>
            
            <div class="input-group" style="margin-top: 24px;">
                <label class="label-v3">سعر التكلفة (عند الأدمن)</label>
                <input type="text" name="prix_admin" required class="input-v3" placeholder="مثال: 120, 150" value="{{ old('prix_admin') }}">
                <p style="text-align: right; font-size: 12px; color: var(--text-light); margin-top: 4px;">يمكنك كتابة عدة أسعار مفصولة بفاصلة.</p>
            </div>
        </div>

        <button type="submit" class="submit-btn" id="submitBtnV3">
            <i class="fas fa-save"></i>
            حفظ ونشر المنتج
        </button>
    </form>
</div>

<!-- Template Safe for Firewall -->
<template id="swatchTemplate">
    <div class="swatch-card active">
        <div class="swatch-circle">
            <i class="fas fa-check"></i>
        </div>
        <span class="swatch-name"></span>
        <input type="checkbox" checked style="display: none;">
        <input type="hidden">
        <div class="stock-container">
            <input type="number" value="0" min="0" class="stock-input-mini" onclick="event.stopPropagation()" oninput="updateTotalStockV3()">
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script>
    function selectSwatch(el, id) {
        const checkbox = el.querySelector('input[type="checkbox"]');
        const stockContainer = el.querySelector('.stock-container');
        
        el.classList.toggle('active');
        checkbox.checked = el.classList.contains('active');
        
        if (el.classList.contains('active')) {
            stockContainer.style.display = 'block';
        } else {
            stockContainer.style.display = 'none';
            el.querySelector('input[type="number"]').value = 0;
        }
        updateTotalStockV3();
    }

    function updateTotalStockV3() {
        let total = 0;
        document.querySelectorAll('.stock-input-mini').forEach(input => {
            const card = input.closest('.swatch-card');
            if (card && card.classList.contains('active')) {
                total += parseInt(input.value) || 0;
            }
        });
        document.getElementById('totalStockValue').textContent = total;
        document.getElementById('total_stock_hidden').value = total;
    }

    function handleFileV3(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview-v3');
                const placeholder = document.getElementById('uploadPlaceholderV3');
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function triggerCustomColor() {
        const name = prompt("اسم اللون المخصص:");
        if (!name) return;
        const hex = prompt("كود اللون (مثال #FF0000):", "#6366F1");
        if (!hex) return;

        const id = Date.now();
        const tpl = document.getElementById('swatchTemplate');
        const clone = tpl.content.cloneNode(true);
        const card = clone.querySelector('.swatch-card');
        
        card.querySelector('.swatch-circle').style.backgroundColor = hex;
        card.querySelector('.swatch-name').textContent = name;
        
        const check = card.querySelector('input[type="checkbox"]');
        check.name = "couleurs[" + id + "]";
        check.value = name;
        
        const hexInp = card.querySelector('input[type="hidden"]');
        hexInp.name = "couleurs_hex[" + id + "]";
        hexInp.value = hex;
        
        const stock = card.querySelector('input[type="number"]');
        stock.name = "stock_couleur_" + id;
        
        card.onclick = function() {
            this.classList.toggle('active');
            check.checked = this.classList.contains('active');
            this.querySelector('.stock-container').style.display = check.checked ? 'block' : 'none';
            if (!check.checked) this.querySelector('input[type="number"]').value = 0;
            updateTotalStockV3();
        };

        document.getElementById('colorPickerGrid').appendChild(clone);
        updateTotalStockV3();
    }
</script>
@endsection
