@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6 md:mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">إنشاء رسالة جديدة</h1>
                        <p class="text-gray-600 mt-2">أرسل رسالة تنبيه أو تهنئة إلى جميع البائعين</p>
                    </div>
                    <a href="{{ route('admin.messages.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>رجوع
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form method="POST" action="{{ route('admin.messages.store') }}" class="space-y-6">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الرسالة *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="مثال: تهانينا على مبيعات هذا الشهر">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الرسالة *</label>
                        <textarea name="message" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="مثال: تهانينا لجميع بائعينا على الأداء المتميز هذا الشهر!">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type & Priority -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع الرسالة *</label>
                            <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر نوعًا</option>
                                <option value="info" @selected(old('type') === 'info')>ℹ️ معلومات</option>
                                <option value="success" @selected(old('type') === 'success')>✅ نجاح</option>
                                <option value="warning" @selected(old('type') === 'warning')>⚠️ تحذير</option>
                                <option value="error" @selected(old('type') === 'error')>❌ خطأ</option>
                                <option value="celebration" @selected(old('type') === 'celebration')>🏆 تهنئة</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الأولوية *</label>
                            <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر أولوية</option>
                                <option value="low" @selected(old('priority') === 'low')>🟢 منخفضة</option>
                                <option value="medium" @selected(old('priority') === 'medium')>🟡 متوسطة</option>
                                <option value="high" @selected(old('priority') === 'high')>🟠 عالية</option>
                                <option value="urgent" @selected(old('priority') === 'urgent')>🔴 عاجلة</option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Targets -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الفئات المستهدفة</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="seller" @checked(in_array('seller', old('target_roles', [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">البائعون</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="admin" @checked(in_array('admin', old('target_roles', [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">المديرون</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="user" @checked(in_array('user', old('target_roles', [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">المستخدمون</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">اتركه فارغًا لاستهداف جميع الأدوار</p>
                        @error('target_roles')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">اتركه فارغًا إذا لم تنته الرسالة</p>
                        @error('expires_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>إرسال الرسالة
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview -->
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">معاينة الرسالة</h3>
                <div id="messagePreview" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <p class="text-gray-500 text-center">املأ النموذج لرؤية المعاينة</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// keep preview logic intact
</script>
@endsection
