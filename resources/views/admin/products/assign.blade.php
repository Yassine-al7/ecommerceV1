@extends('layouts.app')

@section('title', 'Assigner Produit')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Assigner un produit à un vendeur</h1>
            <p class="text-gray-600">Définissez les paramètres d'assignation pour le produit sélectionné</p>
        </div>

        <!-- Informations du produit -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
                <i class="fas fa-box mr-2"></i>
                Informations du produit
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-sm font-medium text-blue-700">Nom:</span>
                    <p class="text-blue-900 font-semibold">{{ $product->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-blue-700">Catégorie:</span>
                    <p class="text-blue-900">{{ $product->category->name ?? 'Non définie' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-blue-700">Stock:</span>
                    <p class="text-blue-900">{{ $product->quantite_stock }} unités</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('admin.products.assign.store', $product) }}" class="space-y-6">
                @csrf

                <!-- Sélection du vendeur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vendeur *</label>
                    <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">-- Choisir un vendeur --</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" @selected(old('user_id') == $seller->id)>
                                {{ $seller->name }} ({{ $seller->email }}) - {{ $seller->store_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix admin (MAD)</label>
                    <input type="number" step="0.01" name="prix_admin"
                           value="{{ old('prix_admin', $product->prix_admin) }}"
                           placeholder="Prix admin du produit"
                           class="mt-1 w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour utiliser le prix du produit</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix vente (MAD)</label>
                    <input type="number" step="0.01" name="prix_vente"
                           value="{{ old('prix_vente', $product->prix_vente) }}"
                           placeholder="Prix vente du produit"
                           class="mt-1 w-full border rounded px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Laissez vide pour utiliser le prix du produit</p>
                </div>
            </div>
                <!-- Visibilité -->
                <div class="flex items-center space-x-3">
                    <input type="checkbox" name="visible" value="1" id="visible"
                           @checked(old('visible', true)) class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="visible" class="text-sm font-medium text-gray-700">Visible pour le vendeur</label>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-link mr-2"></i>Assigner le produit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


