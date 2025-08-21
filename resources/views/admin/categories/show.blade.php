@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
                <p class="text-gray-600 mt-1">Détails de la catégorie</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('admin.categories.edit', $category) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Informations de la catégorie -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Informations Générales</h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Nom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $category->name }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-gray-900">
                            {{ $category->description ?: 'Aucune description' }}
                        </p>
                    </div>

                    <!-- Couleur -->
                    @if($category->color)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Couleur</label>
                        <div class="mt-1 flex items-center space-x-3">
                            <div class="w-8 h-8 rounded border border-gray-300"
                                 style="background-color: {{ $category->color }}"></div>
                            <span class="text-gray-900 font-mono">{{ $category->color }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Statut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Dates -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Historique</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Créée le :</span>
                                {{ $category->created_at ? $category->created_at->format('d/m/Y à H:i') : 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Modifiée le :</span>
                                {{ $category->updated_at ? $category->updated_at->format('d/m/Y à H:i') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produits associés -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800">Produits Associés</h2>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $category->products()->count() }} produit(s)
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($category->products()->take(10)->get() as $product)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <div class="flex items-center space-x-3">
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                         class="w-10 h-10 rounded object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($product->prix_vente, 0) }} MAD</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Stock: {{ $product->quantite_stock }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-3">
                                <i class="fas fa-box-open text-4xl"></i>
                            </div>
                            <p class="text-gray-600">Aucun produit dans cette catégorie</p>
                            <p class="text-sm text-gray-500 mt-1">Les produits apparaîtront ici une fois assignés à cette catégorie</p>
                        </div>
                    @endforelse

                    @if($category->products()->count() > 10)
                        <div class="text-center mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                Et {{ $category->products()->count() - 10 }} produit(s) de plus...
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('admin.categories.edit', $category) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier la Catégorie
                </a>

                @if($category->products()->count() == 0)
                <form action="{{ route('admin.categories.destroy', $category) }}"
                      method="POST"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')"
                      class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Supprimer
                    </button>
                </form>
                @else
                <div class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed" title="Impossible de supprimer une catégorie avec des produits">
                    <i class="fas fa-trash mr-2"></i>
                    Supprimer ({{ $category->products()->count() }} produits)
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
