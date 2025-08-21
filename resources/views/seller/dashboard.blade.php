@extends('layouts.app')

@section('title', 'Dashboard Vendeur')

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Dashboard Vendeur</h1>
            <p class="text-sm md:text-base text-gray-600">Bienvenue {{ auth()->user()->name }} - Vue d'ensemble de votre activité</p>
        </div>

        <!-- Statistiques Principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <!-- Produits Assignés -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-box text-lg md:text-2xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Produits Assignés</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $totalAssignedProducts }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Commandes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-shopping-cart text-lg md:text-2xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Total Commandes</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $totalSellerOrders }}</p>
                    </div>
                </div>
            </div>

            <!-- Chiffre d'Affaires -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-coins text-lg md:text-2xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">Chiffre d'Affaires</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format($totalRevenue, 0) }} MAD</p>
                    </div>
                </div>
            </div>


        </div>

        <!-- Statistiques Commandes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En Attente</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ordersEnAttente }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Livrées</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ordersLivrees }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Annulées</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ordersCancelled }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques Paiements -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Bénéfices Reçus</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPaid, 0) }} MAD</p>
                        <p class="text-xs text-gray-500">Commandes payées</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Bénéfices en Attente</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPending, 0) }} MAD</p>
                        <p class="text-xs text-gray-500">Commandes non payées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
            <a href="{{ route('seller.products.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 md:p-6 text-center transition-colors group">
                <i class="fas fa-box text-2xl md:text-3xl mb-2 md:mb-3 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-base md:text-lg font-semibold">Mes Produits</h3>
                <p class="text-xs md:text-sm opacity-90">Gérer mes produits</p>
            </a>

            <a href="{{ route('seller.orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-4 md:p-6 text-center transition-colors group">
                <i class="fas fa-plus text-2xl md:text-3xl mb-2 md:mb-3 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-base md:text-lg font-semibold">Nouvelle Commande</h3>
                <p class="text-xs md:text-sm opacity-90">Créer une commande</p>
            </a>

            <a href="{{ route('seller.orders.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-4 md:p-6 text-center transition-colors group">
                <i class="fas fa-list text-2xl md:text-3xl mb-2 md:mb-3 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-lg font-semibold">Mes Commandes</h3>
                <p class="text-xs md:text-sm opacity-90">Voir toutes mes commandes</p>
            </a>

            <a href="{{ route('seller.invoices.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white rounded-lg p-4 md:p-6 text-center transition-colors group">
                <i class="fas fa-file-invoice text-2xl md:text-3xl mb-2 md:mb-3 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-lg font-semibold">Facturation</h3>
                <p class="text-xs md:text-sm opacity-90">Voir mes paiements</p>
            </a>
        </div>

        <!-- Commandes Récentes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h2 class="text-base md:text-lg font-semibold text-gray-800">Commandes Récentes</h2>
                    <a href="{{ route('seller.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium self-start sm:self-center">
                        Voir tout <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            @if($recentOrders->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                        <div class="p-4 md:p-6 hover:bg-gray-50">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center space-x-3 md:space-x-4">
                                    <div class="flex-shrink-0">
                                        @if($order->status == 'en attente')
                                            <div class="w-8 h-8 md:w-10 md:h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-clock text-yellow-600 text-sm md:text-base"></i>
                                            </div>
                                        @elseif($order->status == 'livré')
                                            <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check-circle text-green-600 text-sm md:text-base"></i>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 md:w-10 md:h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-times-circle text-red-600 text-sm md:text-base"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $order->reference }}</p>
                                        <p class="text-xs md:text-sm text-gray-500">{{ $order->nom_client }}</p>
                                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-sm font-semibold text-gray-900">{{ number_format($order->prix_commande, 0) }} MAD</p>
                                    @if($order->status == 'livré')
                                        @if($order->facturation_status == 'payé')
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Payé
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>En attente
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                            @if($order->status == 'en attente') bg-yellow-100 text-yellow-800
                                            @elseif($order->status == 'livré') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 md:py-12">
                    <div class="text-gray-400 mb-3 md:mb-4">
                        <i class="fas fa-shopping-cart text-4xl md:text-6xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-medium text-gray-800 mb-2">Aucune commande</h3>
                    <p class="text-sm md:text-base text-gray-600 mb-3 md:mb-4">Vous n'avez pas encore créé de commande.</p>
                    <a href="{{ route('seller.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg font-medium transition-colors text-sm md:text-base">
                        <i class="fas fa-plus mr-2"></i>Créer une commande
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
