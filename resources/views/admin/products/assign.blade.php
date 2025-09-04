@extends('layouts.app')

@section('title', 'إدارة تعيين البائعين')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">إدارة تعيين البائعين</h1>
            <p class="text-gray-600">قم بتعيين أو إزالة تعيين البائعين للمنتج: <strong>{{ $product->name }}</strong></p>
        </div>

        <!-- Informations du produit -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
                <i class="fas fa-box mr-2"></i>
                معلومات المنتج
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-sm font-medium text-blue-700">الاسم:</span>
                    <p class="text-blue-900 font-semibold">{{ $product->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-blue-700">التصنيف:</span>
                    <p class="text-blue-900">{{ $product->category->name ?? 'غير محدد' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-blue-700">المخزون:</span>
                    <p class="text-blue-900">{{ $product->quantite_stock }} وحدة</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-blue-700">تمن البيع:</span>
                    <p class="text-blue-900">{{ number_format($product->prix_admin, 0) }} MAD</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire d'assignation en masse -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('admin.products.assign.store', $product) }}" class="space-y-6">
                @csrf

                <!-- Actions en masse -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <button type="button" id="selectAll" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-check-square mr-1"></i>تحديد الكل
                        </button>
                        <button type="button" id="deselectAll" class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                            <i class="fas fa-square mr-1"></i>إلغاء التحديد
                        </button>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span id="selectedCount">0</span> بائع(ون) محددون
                    </div>
                </div>

                <!-- Liste des vendeurs avec cases à cocher -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">
                        قائمة البائعين
                    </h3>

                    @foreach($sellers as $seller)
                        @php
                            $isAssigned = $product->assignedSellers->contains($seller->id);
                            $assignment = $product->assignedSellers->where('id', $seller->id)->first();
                        @endphp

                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <!-- Case à cocher و infos vendeur -->
                                <div class="flex items-center space-x-4">
                                    <input type="checkbox"
                                           name="selected_sellers[]"
                                           value="{{ $seller->id }}"
                                           id="seller_{{ $seller->id }}"
                                           class="seller-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                           @if($isAssigned) checked @endif>

                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $seller->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $seller->email }}</p>
                                            @if($seller->store_name)
                                                <p class="text-xs text-blue-600">{{ $seller->store_name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Statut d'assignation -->
                                <div class="flex items-center space-x-4">
                                    @if($isAssigned)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>مُعيّن
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i>غير مُعيّن
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Détails de l'assignation (si assigné) -->
                            @if($isAssigned && $assignment)
                                <div class="mt-4 pl-9 border-l-2 border-blue-200 bg-blue-50 p-3 rounded">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-blue-700">تمن البيع:</span>
                                            <p class="text-blue-900">{{ number_format($assignment->pivot->prix_admin ?? $product->prix_admin, 0) }} MAD</p>
                                        </div>
                                        <div>
                                            <span class="font-medium text-blue-700">تمن:</span>
                                            <p class="text-blue-900">{{ number_format($assignment->pivot->prix_vente ?? $product->prix_vente, 0) }} MAD</p>
                                        </div>
                                        <div>
                                            <span class="font-medium text-blue-700">مرئي:</span>
                                            <p class="text-blue-900">
                                                @if($assignment->pivot->visible ?? true)
                                                    <span class="text-green-600">نعم</span>
                                                @else
                                                    <span class="text-red-600">لا</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Paramètres globaux -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">إعدادات التعيين</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">تمن المقترح للبيع (MAD)</label>
                            <input type="text" name="prix_admin"
                                   value="{{ old('prix_admin', $product->prix_admin) }}"
                                   placeholder="مثال: 150-200 أو 150,200,250"
                                   class="mt-1 w-full border rounded px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">يمكنك إدخال سعر واحد أو عدة أسعار مفصولة بفاصلة</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">تمن (MAD)</label>
                            <input type="number" step="0.01" name="prix_vente"
                                   value="{{ old('prix_vente', $product->prix_vente) }}"
                                   placeholder="تمن المنتج"
                                   class="mt-1 w-full border rounded px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">اتركه فارغًا لاستخدام سعر المنتج</p>
                        </div>
                    </div>

                    <!-- Visibilité -->
                    <div class="mt-4 flex items-center space-x-3">
                        <input type="checkbox" name="visible" value="1" id="visible"
                               @checked(old('visible', true)) class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="visible" class="text-sm font-medium text-gray-700">مرئي للبائعين المُعيّنين</label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors text-center">
                        <i class="fas fa-arrow-left mr-2"></i>رجوع
                    </a>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" name="action" value="assign" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-link mr-2"></i>تعيين البائعين المحددين
                        </button>
                        <button type="submit" name="action" value="remove" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-unlink mr-2"></i>إلغاء تعيين البائعين المحددين
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript pour la gestion des cases à cocher -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.seller-checkbox');
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const selectedCount = document.getElementById('selectedCount');

    // Mettre à jour le compteur
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.seller-checkbox:checked').length;
        selectedCount.textContent = checked;
    }

    // Écouter les changements sur chaque case à cocher
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Sélectionner tout
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount();
    });

    // Désélectionner tout
    deselectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });

    // Initialiser le compteur
    updateSelectedCount();
});
</script>
@endsection


