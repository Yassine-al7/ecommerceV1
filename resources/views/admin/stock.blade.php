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
    <div class="container mx-auto mt-5">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Gestion du Stock</h1>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Retour au Dashboard
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800">État du Stock</h2>
            </div>

            @if($products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produit
                                </th>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Couleur
                                </th>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stock Actuel
                                </th>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $product->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $product->couleur }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $product->quantite_stock > 10 ? 'bg-green-100 text-green-800' :
                                               ($product->quantite_stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $product->quantite_stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($product->quantite_stock > 10)
                                            <span class="text-green-600">✓ En stock</span>
                                        @elseif($product->quantite_stock > 0)
                                            <span class="text-yellow-600">⚠ Stock faible</span>
                                        @else
                                            <span class="text-red-600">✗ Rupture</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Réapprovisionner</button>
                                        <button class="text-yellow-600 hover:text-yellow-900">Modifier</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Résumé du stock -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">En Stock</p>
                                <p class="text-lg font-semibold text-green-900">{{ $products->where('quantite_stock', '>', 10)->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">Stock Faible</p>
                                <p class="text-lg font-semibold text-yellow-900">{{ $products->where('quantite_stock', '>', 0)->where('quantite_stock', '<=', 10)->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-red-100 text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">Rupture</p>
                                <p class="text-lg font-semibold text-red-900">{{ $products->where('quantite_stock', 0)->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500 text-lg">Aucun produit trouvé.</p>
                    <p class="text-gray-400 mt-2">Les produits apparaîtront ici une fois qu'ils seront ajoutés.</p>
                </div>
            @endif
        </div>
    </div>
    @endsection
</body>
</html>
