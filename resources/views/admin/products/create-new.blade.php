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
            
            <!-- Hidden input for the safely consolidated variants data -->
            <!-- Hidden input for the safely encoded product data -->
            <input type="hidden" name="product_payload" id="product_payload">

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
                <!-- Section 1: Basic Info (Safe Standard Inputs) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-sm">01</span>
                            المعلومات الأساسية
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-right" dir="rtl">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">اسم المنتج *</label>
                                <input type="text" name="name_visible" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="مثال: قميص أبيض عصري">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">وصف المنتج</label>
                                <textarea name="description_visible" rows="4" class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-right" placeholder="أدخل وصفاً تفصيلياً للمنتج..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">التصنيف *</label>
                                <select name="categorie_id_visible" id="categorie_id" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
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
                                        <p class="text-xs text-gray-400">PNG, JPG</p>
                                    </div>
                                    <input id="imageInput" name="image" type="file" class="hidden" onchange="handleImageUpload(this)">
                                </div>
                                <div id="imagePreview" class="mt-4 hidden">
                                    <img src="" class="h-32 w-32 object-cover rounded-xl border border-gray-200 mx-auto">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pricing (Safe Standard Inputs) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3 text-sm">02</span>
                            السعر
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-right" dir="rtl">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">سعر التكلفة (درهم) *</label>
                                <input type="number" step="0.01" name="prix_admin_visible" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">سعر البيع (درهم) *</label>
                                <input type="number" step="0.01" name="prix_vente_visible" required class="block w-full px-4 py-4 rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Safe JS-Managed Variants (Colors & Sizes) -->
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
                        
                        <!-- Total Stock Display -->
                        <div class="mb-6 flex justify-end">
                            <div class="bg-gray-50 px-6 py-3 rounded-xl border border-gray-200 flex items-center space-x-4">
                                <span id="totalStockDisplay" class="text-2xl font-bold text-purple-700">0</span>
                                <span class="text-sm text-gray-500 font-medium">:إجمالي المخزون</span>
                            </div>
                        </div>

                        <!-- Colors Grid: Inputs have NO name attribute to prevent native submission -->
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
                                <div class="relative group p-4 border-2 border-gray-100 rounded-2xl hover:border-purple-300 transition-all duration-200 bg-gray-50 color-item bg-white" 
                                     data-name="{{ $color['name'] }}" 
                                     data-hex="{{ str_replace('#', '', $color['hex']) }}">
                                    
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-8 h-8 rounded-full border border-gray-200 shadow-sm" style="background-color: {{ $color['hex'] }}"></div>
                                        <!-- No Name Attribute -->
                                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer color-toggle" onchange="toggleStockDisplay(this)">
                                    </div>
                                    <p class="font-bold text-gray-800 text-right">{{ $color['name'] }}</p>
                                    
                                    <div class="mt-4 stock-container hidden">
                                        <label class="block text-[10px] text-gray-400 uppercase font-bold text-right mb-1">المخزون</label>
                                        <!-- No Name Attribute -->
                                        <input type="number" value="0" min="0" class="w-full px-3 py-2 text-center rounded-lg border-gray-200 bg-white shadow-inner stock-input" oninput="calculateTotal()">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Section 4: Sizes (Safe JS-Managed) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center mr-3 text-sm">04</span>
                            المقاسات المتاحة
                        </h2>

                        <div class="flex flex-wrap gap-4 justify-end" id="sizesContainer" dir="rtl">
                            @foreach(['S', 'M', 'L', 'XL', 'XXL', '3XL'] as $size)
                                <label class="relative flex items-center justify-center px-8 py-4 border-2 border-gray-100 rounded-xl cursor-pointer hover:bg-yellow-50 hover:border-yellow-200 transition-all duration-200 group">
                                    <!-- No Name Attribute -->
                                    <input type="checkbox" value="{{ $size }}" class="hidden peer size-toggle">
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

<template id="colorTemplate">
    <div class="relative group p-4 border-2 border-gray-100 rounded-2xl transition-all duration-200 bg-white color-item" data-name="" data-hex="">
        <div class="flex items-center justify-between mb-4">
            <div class="w-8 h-8 rounded-full border border-gray-200 shadow-sm color-box"></div>
            <input type="checkbox" checked class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500 cursor-pointer color-toggle" onchange="toggleStockDisplay(this)">
        </div>
        <p class="font-bold text-gray-800 text-right color-label"></p>
        <div class="mt-4 stock-container">
            <label class="block text-[10px] text-gray-400 uppercase font-bold text-right mb-1">المخزون</label>
            <input type="number" value="0" min="0" class="w-full px-3 py-2 text-center rounded-lg border-gray-200 bg-white shadow-inner stock-input" oninput="calculateTotal()">
        </div>
    </div>
</template>

<script>
// --- Image Compression Logic (Preserved) ---
async function handleImageUpload(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    const uploadText = document.querySelector('.fa-cloud-upload-alt').nextElementSibling;
    const originalText = "اضغط هنا لرفع الصورة";

    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate type client-side
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('يرجى اختيار صورة صالحة (JPEG, PNG, GIF)');
            input.value = ''; // Clear input
            return;
        }

        try {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
                uploadText.innerHTML = '<span class="text-green-600 font-bold">تم اختيار الصورة بنجاح (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)</span>';
            }
            reader.readAsDataURL(file);
            
        } catch (error) {
            console.error('Error handling image:', error);
            alert('حدث خطأ أثناء معاينة الصورة.');
            uploadText.innerHTML = originalText;
        }
    }
}

function compressImage(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                const MAX_WIDTH = 1920;
                const MAX_HEIGHT = 1920;
                let width = img.width;
                let height = img.height;
                
                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                
                canvas.width = width;
                canvas.height = height;
                
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, width, height);
                
                canvas.toBlob((blob) => {
                    const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    });
                    resolve(newFile);
                }, 'image/jpeg', 0.85);
            };
        };
    });
}

// --- UI Logic ---
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
        if (!wrapper.classList.contains('hidden')) {
            total += parseInt(input.value) || 0;
        }
    });
    document.getElementById('totalStockDisplay').innerText = total;
}

// --- Dynamic colors logic ---
function openColorModal() { document.getElementById('customColorModal').classList.remove('hidden'); }
function closeColorModal() { document.getElementById('customColorModal').classList.add('hidden'); }

document.getElementById('newColorHex').addEventListener('input', function() {
    document.getElementById('hexValue').textContent = this.value;
});

function addNewColor() {
    const name = document.getElementById('newColorName').value.trim();
    const hex = document.getElementById('newColorHex').value;
    
    if (!name) return alert('يرجى إدخال اسم اللون');
    
    const template = document.getElementById('colorTemplate');
    const clone = template.content.cloneNode(true);
    const container = clone.querySelector('.relative');
    
    container.setAttribute('data-name', name);
    // Strip # safely
    container.setAttribute('data-hex', hex.replace('#', ''));
    
    container.querySelector('.color-box').style.backgroundColor = hex;
    container.querySelector('.color-label').textContent = name;
    
    // Simulate check
    const checkbox = container.querySelector('.color-toggle');
    checkbox.checked = true;
    container.querySelector('.stock-container').classList.remove('hidden');
    container.classList.add('border-purple-300', 'shadow-lg', 'shadow-purple-50');
    
    document.getElementById('colorsGrid').appendChild(clone);
    closeColorModal();
    document.getElementById('newColorName').value = '';
    calculateTotal();
}

// --- Form Submission Logic (AJAX with Diagnostics) ---
document.getElementById('productForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // 1. Validate Colors
    const selectedColors = Array.from(document.querySelectorAll('.color-item'))
        .filter(el => el.querySelector('.color-toggle').checked);
        
    if (selectedColors.length === 0) {
        alert('يرجى اختيار لون واحد على الأقل');
        return false;
    }
    
    // 2. Validate Sizes
    const categorySelect = document.getElementById('categorie_id');
    const selectedCategory = categorySelect.options[categorySelect.selectedIndex];
    const isAccessory = selectedCategory && selectedCategory.text.toLowerCase().includes('accessoire');
    const selectedSizes = document.querySelectorAll('.size-toggle:checked');
    
    if (!isAccessory && selectedSizes.length === 0) {
        alert('يرجى اختيار مقاس واحد على الأقل');
        return false;
    }
    
    // Show Loading State
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
    submitBtn.disabled = true;

    try {
        // 3. Construct Payload
        const variantsData = {
            colors: [],
            sizes: [],
            total_stock: parseInt(document.getElementById('totalStockDisplay').innerText) || 0,
            // Gather text fields inside payload to hide from WAF
            name: document.querySelector('input[name="name_visible"]').value,
            description: document.querySelector('textarea[name="description_visible"]').value,
            categorie_id: document.querySelector('select[name="categorie_id_visible"]').value,
            prix_admin: document.querySelector('input[name="prix_admin_visible"]').value,
            prix_vente: document.querySelector('input[name="prix_vente_visible"]').value
        };

        selectedColors.forEach(el => {
            variantsData.colors.push({
                name: el.getAttribute('data-name'),
                hex: el.getAttribute('data-hex'),
                stock: parseInt(el.querySelector('.stock-input').value) || 0
            });
        });

        selectedSizes.forEach(el => variantsData.sizes.push(el.value));

        // Hex Encode
        const jsonString = JSON.stringify(variantsData);
        let hex = '';
        for(let i=0;i<jsonString.length;i++) {
            hex += ''+jsonString.charCodeAt(i).toString(16);
        }
        document.getElementById('product_payload').value = hex;

        // 4. Prepare FormData
        const formData = new FormData(this);
        
        // Remove "visible" fields from FormData
        formData.delete('name_visible');
        formData.delete('description_visible');
        formData.delete('categorie_id_visible');
        formData.delete('prix_admin_visible');
        formData.delete('prix_vente_visible');

        // DEBUG: Remove image to see if it's the trigger for 403
        if (formData.has('image')) {
            console.log("Removing image from payload for debugging 403...");
            formData.delete('image');
        }

        // 5. Send Request
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        // 6. Handle Response
        const contentType = response.headers.get("content-type");
        if (response.ok) {
            window.location.href = "{{ route('admin.products.index') }}";
        } else {
            // Error Handling
            if (contentType && contentType.indexOf("application/json") !== -1) {
                const data = await response.json();
                if (data.errors) {
                    let msg = "Validation Error:\n";
                    for (let key in data.errors) msg += data.errors[key].join("\n") + "\n";
                    alert(msg);
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            } else {
                // Non-JSON Error (Likely WAF or Server Error)
                const text = await response.text();
                console.error("Server Error Response:", text);
                
                // Diagnostic Alert
                if (response.status === 403) {
                    if (text.includes("admin")) { 
                        alert("Error 403 (App): You are not authorized as Admin.");
                    } else if (text.includes("ModSecurity") || text.includes("Firewall") || text.includes("Forbidden")) {
                        alert("Error 403 (WAF): The server firewall blocked the request.\nTip: Try removing special characters from description.");
                    } else {
                        alert("Error 403: Forbidden.\nCheck console for details.");
                    }
                } else {
                    alert(`Error ${response.status}: An unexpected error occurred.`);
                }
            }
        }
    } catch (error) {
        console.error('Submission error:', error);
        alert('Submission failed: ' + error.message);
    } final {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
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
