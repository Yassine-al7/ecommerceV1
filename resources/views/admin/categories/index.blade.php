@extends('layouts.app')

@section('title', __('admin_categories.title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('admin_categories.title') }}</h1>
                <p class="text-gray-600">{{ __('admin_categories.subtitle') }}</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                <i class="fas fa-plus mr-2"></i>{{ __('admin_categories.new') }}
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Table des catégories -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_categories.table.name') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_categories.table.description') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_categories.table.slug') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_categories.table.products') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_categories.table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ Str::limit($category->description, 50) ?: __('admin_categories.table.empty_description') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $category->slug ?: __('admin_categories.table.empty_slug') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('admin_categories.table.products_badge', ['count' => $category->products_count]) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                           class="text-blue-600 hover:text-blue-900" title="{{ __('admin_categories.table.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('{{ __('admin_categories.table.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="{{ __('admin_categories.table.delete') }}"
                                                    {{ $category->products_count > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center py-8">
                                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-500">{{ __('admin_categories.none_found_title') }}</p>
                                        <p class="text-sm text-gray-400 mt-2">{{ __('admin_categories.none_found_hint') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
