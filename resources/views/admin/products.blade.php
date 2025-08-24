<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('title', 'Gestion des Produits')

    @php
    use App\Helpers\ColorHelper;
    @endphp

    @section('content')
    <div class="min-h-screen bg-gray-50 py-4 md:py-8">
        <div class="container-responsive">
            <!-- Header -->
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 text-center md:text-left">Gestion des Produits</h1>
                <p class="text-gray-600 text-center md:text-left">Gérez votre catalogue de produits et assignez-les aux vendeurs</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success mb-6">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-lg p-4 md:p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-lg md:text-xl font-semibold text-gray-800 text-center md:text-left">Liste des Produits</h2>
                        <p class="text-gray-600 mt-1 text-center md:text-left">Total: {{ $products->count() }} produits</p>
                    </div>
                    <div class="actions-buttons">
                        <a href="{{ route('admin.products.create') }}" class="btn bg-blue-600 hover:bg-blue-700 text-white">
                            <i class="fas fa-plus mr-2"></i>Créer un produit
                        </a>
                    </div>
                </div>
            </div>

            @if($products->count() > 0)
                <!-- Grille des produits -->
                <div class="products-grid">
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

                                <!-- Couleurs -->
                                @if($product->couleur_filtree ?? $product->couleur)
                                    <div class="mb-3">
                                        <span class="text-sm text-gray-600 mr-2">Couleurs:</span>
                                        @php
                                            // Utiliser les couleurs filtrées si disponibles, sinon les originales
                                            $couleurs = ColorHelper::decodeColors($product->couleur_filtree ?? $product->couleur);
                                        @endphp

                                        <div class="flex flex-wrap gap-2 mt-1">
                                            @foreach($couleurs as $couleur)
                                                @php
                                                    // Gérer le nouveau format des couleurs (objets avec name et hex)
                                                    if (is_array($couleur) && isset($couleur['name'])) {
                                                        $couleurName = $couleur['name'];
                                                        $couleurHex = $couleur['hex'] ?? null;
                                                    } else {
                                                        $couleurName = is_string($couleur) ? $couleur : '';
                                                        $couleurHex = null;
                                                    }
                                                    $backgroundColor = $couleurHex ?: ColorHelper::getBackgroundColor($couleurName);
                                                @endphp
                                                <div class="flex items-center space-x-1">
                                                    <div class="w-4 h-4 rounded-full border border-gray-300 shadow-sm"
                                                         style="background-color: {{ $backgroundColor }}"></div>
                                                    <span class="text-xs text-gray-700">{{ $couleurName }}</span>
                                                </div>
                                            @endforeach
                                        </div>
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
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pt-3 border-t border-gray-100 gap-2">
                                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                                        <a href="{{ route('admin.products.assign', $product) }}"
                                           class="btn btn-sm text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100">
                                            <i class="fas fa-link mr-1"></i>Assigner
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                           class="btn btn-sm text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100">
                                            <i class="fas fa-edit mr-1"></i>Modifier
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                                <i class="fas fa-trash mr-1"></i>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                            @endforeach
                </div>
            @else
                <!-- État vide -->
                <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-box-open text-4xl md:text-6xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-medium text-gray-800 mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-600 mb-6">Commencez par créer votre premier produit pour enrichir votre catalogue.</p>
                    <div class="actions-buttons justify-center">
                        <a href="{{ route('admin.products.create') }}"
                           class="btn bg-blue-600 hover:bg-blue-700 text-white">
                            <i class="fas fa-plus mr-2"></i>Créer votre premier produit
                        </a>
                    </div>
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
