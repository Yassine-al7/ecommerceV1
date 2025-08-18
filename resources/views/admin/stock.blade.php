<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestion du Stock</h1>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Produit
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Catégorie
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Stock
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Prix Admin
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Prix Vente
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center">
                                        @if($product->image)
                                            <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ $product->image }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->couleur }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm text-gray-900">{{ $product->category->name ?? 'Sans catégorie' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $product->quantite_stock }}</span>
                                        @if($product->quantite_stock <= 5)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Faible
                                            </span>
                                        @elseif($product->quantite_stock <= 20)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Moyen
                                            </span>
                                        @else
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Bon
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm text-gray-900 font-medium">{{ number_format($product->prix_admin, 2) }} MAD</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm text-gray-900 font-medium">{{ number_format($product->prix_vente, 2) }} MAD</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($product->quantite_stock > 0) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($product->quantite_stock > 0)
                                            En stock
                                        @else
                                            Rupture
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Aucun produit trouvé
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">Total Produits</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $products->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">En Stock</p>
                            <p class="text-2xl font-bold text-green-900">{{ $products->where('quantite_stock', '>', 0)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Rupture</p>
                            <p class="text-2xl font-bold text-red-900">{{ $products->where('quantite_stock', '<=', 0)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</body>
</html>
