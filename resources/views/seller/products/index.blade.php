@extends('layouts.app')

@section('title', 'Mes Produits Assignés')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Mes Produits Assignés</h1>
            <p class="text-gray-600">Consultez tous les produits qui vous ont été assignés par l'administrateur</p>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600">Total Produits</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $products->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-eye text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600">Visibles</p>
                        <p class="text-2xl font-bold text-green-900">{{ $products->where('pivot.visible', true)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-eye-slash text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-600">Masqués</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $products->where('pivot.visible', false)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-tags text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-purple-600">Valeur Totale</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($products->sum('pivot.prix_vente'), 2) }} MAD</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les catégories</option>
                        @foreach($products->pluck('category.name')->unique()->filter() as $categoryName)
                            <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Visibilité</label>
                    <select id="visibilityFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous</option>
                        <option value="visible">Visibles</option>
                        <option value="hidden">Masqués</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input type="text" id="searchFilter" placeholder="Nom du produit..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Grille des produits -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="productsGrid">
            @forelse($products as $product)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden product-card"
                     data-category="{{ $product->category->name ?? 'Sans catégorie' }}"
                     data-visibility="{{ $product->pivot->visible ? 'visible' : 'hidden' }}"
                     data-name="{{ strtolower($product->name) }}">

                    <!-- Image du produit -->
                    <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                                                @php
                            // Gérer les différents formats de chemins d'images
                            $imagePath = $product->image;
                            $imageSrc = null;

                            if ($imagePath && !empty(trim($imagePath))) {
                                // Si le chemin commence déjà par /storage/, l'utiliser tel quel
                                if (str_starts_with($imagePath, '/storage/')) {
                                    $imageSrc = $imagePath;
                                }
                                // Si c'est juste le nom du fichier, ajouter /storage/products/
                                elseif (!str_contains($imagePath, '/')) {
                                    $imageSrc = '/storage/products/' . $imagePath;
                                }
                                // Sinon utiliser tel quel
                                else {
                                    $imageSrc = $imagePath;
                                }
                            }
                        @endphp

                        @if($imageSrc)
                            <img src="{{ $imageSrc }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="text-gray-400 text-center absolute inset-0 items-center justify-center hidden">
                                <i class="fas fa-image text-4xl mb-2"></i>
                                <p class="text-sm">Image manquante</p>
                                <p class="text-xs text-red-400 mt-1">Fichier introuvable</p>
                            </div>
                        @else
                            <div class="text-gray-400 text-center">
                                <i class="fas fa-image text-4xl mb-2"></i>
                                <p class="text-sm">Aucune image</p>
                            </div>
                        @endif
                    </div>

                    <!-- Informations du produit -->
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-800 line-clamp-2">{{ $product->name }}</h3>
                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full
                                {{ $product->pivot->visible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->pivot->visible ? 'Visible' : 'Masqué' }}
                            </span>
                        </div>

                        @if($product->category)
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-tag mr-1"></i>{{ $product->category->name }}
                            </p>
                        @endif

                        <div class="space-y-2 mb-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Prix Admin:</span>
                                <span class="font-semibold text-gray-800">{{ number_format($product->pivot->prix_admin ?? 0, 0) }} MAD</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Prix Vente:</span>
                                <span class="font-semibold text-blue-600 text-lg">{{ number_format($product->pivot->prix_vente ?? 0, 0) }} MAD</span>
                            </div>
                        </div>

                        @if($product->couleur)
                            <div class="flex items-center mb-2">
                                <span class="text-sm text-gray-600 mr-2">Couleur:</span>
                                @php
                                    // Déterminer si c'est une couleur hex ou un nom
                                    $isHexColor = str_starts_with($product->couleur, '#') && strlen($product->couleur) === 7;
                                    $displayColor = $isHexColor ? $product->couleur : $product->couleur;

                                    // Mapper les noms de couleurs vers des codes hex
                                    $colorMap = [
                                        'rouge' => '#ff0000', 'vert' => '#00ff00', 'bleu' => '#0000ff',
                                        'jaune' => '#ffff00', 'noir' => '#000000', 'blanc' => '#ffffff',
                                        'orange' => '#ffa500', 'violet' => '#800080', 'rose' => '#ffc0cb',
                                        'marron' => '#a52a2a', 'gris' => '#808080', 'beige' => '#f5f5dc'
                                    ];

                                    $backgroundColor = $isHexColor ? $product->couleur :
                                        (isset($colorMap[strtolower($product->couleur)]) ? $colorMap[strtolower($product->couleur)] : '#cccccc');
                                @endphp

                                <div class="w-6 h-6 rounded-full border-2 border-gray-300 shadow-sm flex items-center justify-center"
                                     style="background-color: {{ $backgroundColor }}">
                                    @if($isHexColor)
                                        <!-- Si c'est une couleur hex, afficher un petit indicateur -->
                                        <div class="w-2 h-2 rounded-full bg-white opacity-80"></div>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-800">{{ ucfirst($product->couleur) }}</span>
                            </div>
                        @endif

                        @if($product->tailles && is_array($product->tailles))
                            <div class="mb-2">
                                <span class="text-sm text-gray-600">Tailles disponibles:</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($product->tailles as $taille)
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">{{ $taille }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="text-xs text-gray-500 text-center pt-2 border-t border-gray-100">
                            <i class="fas fa-calendar mr-1"></i>
                            Assigné le {{ $product->pivot->created_at ? $product->pivot->created_at->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <i class="fas fa-box-open text-6xl mb-4 opacity-50"></i>
                        <h3 class="text-xl font-medium mb-2">Aucun produit assigné</h3>
                        <p class="text-gray-600">Vous n'avez pas encore de produits assignés par l'administrateur.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('categoryFilter');
    const visibilityFilter = document.getElementById('visibilityFilter');
    const searchFilter = document.getElementById('searchFilter');
    const productsGrid = document.getElementById('productsGrid');
    const productCards = document.querySelectorAll('.product-card');

    function filterProducts() {
        const selectedCategory = categoryFilter.value;
        const selectedVisibility = visibilityFilter.value;
        const searchTerm = searchFilter.value.toLowerCase();

        productCards.forEach(card => {
            const category = card.dataset.category;
            const visibility = card.dataset.visibility;
            const name = card.dataset.name;

            const categoryMatch = !selectedCategory || category === selectedCategory;
            const visibilityMatch = !selectedVisibility || visibility === selectedVisibility;
            const searchMatch = !searchTerm || name.includes(searchTerm);

            if (categoryMatch && visibilityMatch && searchMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    categoryFilter.addEventListener('change', filterProducts);
    visibilityFilter.addEventListener('change', filterProducts);
    searchFilter.addEventListener('input', filterProducts);
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
