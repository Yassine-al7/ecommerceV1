@extends('layouts.app')

@section('title', __('seller_products.title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
    <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-blue-900 shadow-sm text-center" dir="rtl">
                <h1 class="text-2xl md:text-3xl font-extrabold mb-1">{{ __('seller_products.title') }}</h1>
                <p class="text-sm md:text-base leading-relaxed">{{ __('seller_products.subtitle') }}</p>
            </div>
        </div>



    @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

        <!-- Cartes de Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-boxes text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller_products.stats.total') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $products->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-coins text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller_products.stats.value') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format((float)$products->sum('prix_vente'), 2) }} MAD</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-tags text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller_products.stats.categories') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $products->pluck('category.name')->unique()->filter()->count() }}</p>
                    </div>
                </div>
            </div>


        </div>

        <!-- Filtres -->
        <form method="GET" action="{{ route('seller.products.index') }}" id="filterForm" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('seller_products.filters.category') }}</label>
                    <select name="category" id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">{{ __('seller_products.filters.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('seller_products.filters.search') }}</label>
                    <input type="text" name="search" id="searchFilter" placeholder="{{ __('seller_products.filters.search_ph') }}" value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Boutons d'action des filtres -->
            <div class="flex justify-between items-center mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-filter mr-2"></i>{{ __('seller_products.filters.apply') }}
                </button>
                <a href="{{ route('seller.products.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-times mr-2"></i>{{ __('seller_products.filters.reset') }}
                </a>
            </div>
        </form>

        <!-- Grille des produits modernes -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="productsGrid">
            @forelse($products as $product)
                <x-modern-product-card :product="$product" user-type="seller" />
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <i class="fas fa-box-open text-6xl mb-4 opacity-50"></i>
                        <h3 class="text-xl font-medium mb-2">{{ __('seller_products.empty.title') }}</h3>
                        <p class="text-gray-600">{{ __('seller_products.empty.subtitle') }}</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
// Filtrage automatique sans rechargement de page
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('categoryFilter');
    const searchFilter = document.getElementById('searchFilter');
    const filterForm = document.getElementById('filterForm');

    // Fonction pour appliquer les filtres automatiquement
    function applyFilters() {
        // Construire l'URL avec les paramètres
        const params = new URLSearchParams();

        if (categoryFilter.value) params.append('category', categoryFilter.value);
        if (searchFilter.value) params.append('search', searchFilter.value);

        // Rediriger vers la page avec les filtres
        const url = new URL(window.location);
        url.search = params.toString();
        window.location.href = url.toString();
    }

    // Événements pour filtrage automatique
    categoryFilter.addEventListener('change', applyFilters);

    // Recherche avec délai pour éviter trop de requêtes
    let searchTimeout;
    searchFilter.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });

    // Empêcher la soumission du formulaire (on utilise le filtrage automatique)
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}
</style>
@endsection
