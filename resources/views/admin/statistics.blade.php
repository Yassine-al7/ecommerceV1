<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Admin Panel</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Statistiques</h1>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Retour au Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Carte Total Commandes -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Commandes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Order::count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Carte Total Vendeurs -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Vendeurs</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\User::where('role', 'seller')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Carte Total Produits -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Produits</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Product::count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Carte Chiffre d'Affaires -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Chiffre d'Affaires</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format(\App\Models\Order::sum('prix_commande'), 2) }} €</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques et tableaux détaillés -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Commandes par statut -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Commandes par Statut</h3>
                <div class="space-y-3">
                    @php
                        $statuses = \App\Models\Order::selectRaw('status, COUNT(*) as count')
                            ->groupBy('status')
                            ->get();
                    @endphp

                    @foreach($statuses as $status)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ ucfirst($status->status) }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $status->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Produits par catégorie -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Produits par Catégorie</h3>
                <div class="space-y-3">
                    @php
                        $categories = \App\Models\Product::selectRaw('categorie_id, COUNT(*) as count')
                            ->groupBy('categorie_id')
                            ->get();
                    @endphp

                    @foreach($categories as $category)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Catégorie {{ $category->categorie_id }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $category->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endsection
</body>
</html>
