@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ __('admin_categories.form.header_edit') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('admin_categories.form.header_info') }}: <strong>{{ $category->name }}</strong></p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('admin.categories.show', $category) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    {{ __('admin_categories.form.view') }}
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('admin_categories.form.back') }}
                </a>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">{{ __('admin_categories.form.header_info') }}</h2>
            </div>

            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Nom de la catégorie -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_categories.form.name_required') }}</label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $category->name) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="{{ __('admin_categories.form.name_placeholder') }}">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_categories.form.description') }} <span class="text-gray-500">({{ __('admin_categories.form.description_optional') }})</span></label>
                    <textarea id="description"
                              name="description"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="{{ __('admin_categories.form.description_placeholder') }}">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>



                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_categories.form.status') }}</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $category->is_active) == '1' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ __('admin_categories.form.active') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio"
                                   name="is_active"
                                   value="0"
                                   {{ old('is_active', $category->is_active) == '0' ? 'checked' : '' }}
                                   class="mr-2 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ __('admin_categories.form.inactive') }}</span>
                        </label>
                    </div>
                    @error('is_active')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informations de création/modification -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('admin_categories.form.info_block') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">{{ __('admin_categories.form.created_at') }}:</span>
                            {{ $category->created_at ? $category->created_at->format('d/m/Y à H:i') : __('admin_categories.form.na') }}
                        </div>
                        <div>
                            <span class="font-medium">{{ __('admin_categories.form.updated_at') }}:</span>
                            {{ $category->updated_at ? $category->updated_at->format('d/m/Y à H:i') : __('admin_categories.form.na') }}
                        </div>
                        <div>
                            <span class="font-medium">{{ __('admin_categories.form.associated_products') }}:</span>
                            {{ __('admin_categories.table.products_badge', ['count' => $category->products()->count()]) }}
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.categories.index') }}"
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                        {{ __('admin_categories.form.cancel') }}
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        {{ __('admin_categories.form.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
