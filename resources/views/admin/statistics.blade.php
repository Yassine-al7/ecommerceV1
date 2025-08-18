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
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Tableau de Bord - Statistiques</h1>

            <!-- Statistiques des commandes par statut -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach(['en attente', 'en cours', 'livré', 'annulé'] as $status)
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full
                                @if($status == 'en attente') bg-yellow-100 text-yellow-600
                                @elseif($status == 'en cours') bg-blue-100 text-blue-600
                                @elseif($status == 'livré') bg-green-100 text-green-600
                                @else bg-red-100 text-red-600
                                @endif">
                                <i class="fas
                                    @if($status == 'en attente') fa-clock
                                    @elseif($status == 'en cours') fa-truck
                                    @elseif($status == 'livré') fa-check-circle
                                    @else fa-times-circle
                                    @endif text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Commandes {{ ucfirst($status) }}</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $orderStats[$status] ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Chiffre d'affaires et produits -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Chiffre d'affaires -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Chiffre d'Affaires</h3>
                    <div class="text-3xl font-bold text-green-600 mb-2">
                        {{ number_format($totalRevenue, 2) }} MAD
                    </div>
                    <p class="text-sm text-gray-600">Total des commandes livrées</p>
                </div>

                <!-- Total des produits -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Produits</h3>
                    <div class="text-3xl font-bold text-blue-600 mb-2">
                        {{ $totalProducts }}
                    </div>
                    <p class="text-sm text-gray-600">Total des produits en stock</p>
                </div>
            </div>

            <!-- Top produits vendus -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Produits Vendus</h3>
                <div class="space-y-3">
                    @forelse($topProducts as $product)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-lg font-semibold text-gray-400 mr-3">#{{ $loop->iteration }}</span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $product->category->name ?? 'Sans catégorie' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ $product->total_sales ?? 0 }}</p>
                                <p class="text-sm text-gray-600">ventes</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Aucun produit vendu pour le moment</p>
                    @endforelse
                </div>
            </div>

            <!-- Activités récentes -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Activités Récentes</h3>
                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full
                                    @if($order->status == 'en attente') bg-yellow-500
                                    @elseif($order->status == 'en cours') bg-blue-500
                                    @elseif($order->status == 'livré') bg-green-500
                                    @else bg-red-500
                                    @endif mr-3"></div>
                                <div>
                                    <p class="font-medium text-gray-900">Commande #{{ $order->reference }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $order->nom_client }} - {{ $order->seller->name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ number_format($order->prix_commande, 2) }} MAD</p>
                                <p class="text-sm text-gray-600">{{ ucfirst($order->status) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Aucune commande récente</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endsection
</body>
</html>
