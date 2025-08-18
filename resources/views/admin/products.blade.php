<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('title', 'Gestion des Produits')

    @section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestion des Produits</h1>
                <p class="text-gray-600">Gérez votre catalogue de produits et assignez-les aux vendeurs</p>
        </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                <h2 class="text-xl font-semibold text-gray-800">Liste des Produits</h2>
                        <p class="text-gray-600 mt-1">Total: {{ $products->count() }} produits</p>
                    </div>
                    <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i>Créer un produit
                    </a>
                </div>
            </div>

            @if($products->count() > 0)
                <!-- Grille des produits -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($products as $product)
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
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

                                <!-- Badge de statut -->
                                <div class="absolute top-2 right-2">
                                    @if($product->assignedUsers && $product->assignedUsers->count() > 0)
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                            Assigné ({{ $product->assignedUsers->count() }})
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                            Non assigné
                                        </span>
                                    @endif
                                </div>

                                <!-- Debug info (à supprimer en production) -->
                                @if(config('app.debug'))
                                    <div class="absolute top-2 left-2 text-xs text-gray-500">
                                        ID: {{ $product->id }}<br>
                                        Assigned: {{ $product->assignedUsers ? $product->assignedUsers->count() : 0 }}
                                    </div>
                                @endif
                            </div>

                            <!-- Informations du produit -->
                            <div class="p-4">
                                <div class="mb-3">
                                    <h3 class="text-lg font-semibold text-gray-800 line-clamp-2 mb-1">{{ $product->name }}</h3>
                                    @if($product->category)
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-tag mr-1"></i>{{ $product->category->name }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Couleur -->
                                @if($product->couleur)
                                    <div class="flex items-center mb-3">
                                        <span class="text-sm text-gray-600 mr-2">Couleur:</span>
                                        @php
                                            // Déterminer si c'est une couleur hex ou un nom
                                            $isHexColor = str_starts_with($product->couleur, '#') && strlen($product->couleur) === 7;

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

                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 shadow-sm flex items-center justify-center"
                                             style="background-color: {{ $backgroundColor }}">
                                            @if($isHexColor)
                                                <div class="w-2 h-2 rounded-full bg-white opacity-80"></div>
                                            @endif
                                        </div>
                                        <span class="ml-2 text-sm font-medium text-gray-800">{{ ucfirst($product->couleur) }}</span>
                                    </div>
                                @endif

                                <!-- Prix et stock -->
                                <div class="space-y-2 mb-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Prix Admin:</span>
                                        <span class="font-semibold text-gray-800">{{ number_format($product->prix_admin, 0) }} MAD</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Prix Vente:</span>
                                        <span class="font-semibold text-blue-600">{{ number_format($product->prix_vente, 0) }} MAD</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Stock:</span>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $product->quantite_stock > 10 ? 'bg-green-100 text-green-800' :
                                               ($product->quantite_stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $product->quantite_stock }} unités
                                        </span>
                                    </div>
                                </div>

                                <!-- Tailles -->
                                @if($product->tailles && is_array($product->tailles))
                                    <div class="mb-3">
                                        <span class="text-sm text-gray-600">Tailles:</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach(array_slice($product->tailles, 0, 4) as $taille)
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">{{ $taille }}</span>
                                            @endforeach
                                            @if(count($product->tailles) > 4)
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">+{{ count($product->tailles) - 4 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.products.assign', $product) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            <i class="fas fa-link mr-1"></i>Assigner
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            <i class="fas fa-edit mr-1"></i>Modifier
                                        </a>
                                    </div>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 text-sm font-medium"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                            <i class="fas fa-trash mr-1"></i>Supprimer
                                        </button>
                                        </form>
                                </div>
                            </div>
                        </div>
                            @endforeach
                </div>
            @else
                <!-- État vide -->
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-box-open text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-800 mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-600 mb-6">Commencez par créer votre premier produit pour enrichir votre catalogue.</p>
                    <a href="{{ route('admin.products.create') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>Créer votre premier produit
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>
    @endsection
</body>
</html>
