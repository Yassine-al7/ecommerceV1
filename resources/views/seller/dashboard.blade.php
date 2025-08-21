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

        <!-- Messages Admin -->
        @if($adminMessages->count() > 0)
            <div class="mb-6 md:mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 md:p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 md:p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-bullhorn text-lg md:text-xl"></i>
                        </div>
                        <h2 class="ml-3 text-lg md:text-xl font-semibold text-blue-800">Messages de l'Administration</h2>
                    </div>

                    <div class="space-y-3">
                        @foreach($adminMessages as $message)
                            <div class="bg-white rounded-lg border-l-4 {{ $message->getPriorityClass() }} p-3 md:p-4 shadow-sm">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <i class="{{ $message->getIcon() }} mr-2 text-{{ $message->type === 'celebration' ? 'purple' : ($message->type === 'success' ? 'green' : ($message->type === 'warning' ? 'yellow' : ($message->type === 'error' ? 'red' : 'blue'))) }}-600"></i>
                                            <h3 class="font-semibold text-gray-800">{{ $message->title }}</h3>
                                            @if($message->isUrgent())
                                                <span class="ml-2 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">URGENT</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">{{ $message->message }}</p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="mr-3">
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ ucfirst($message->type) }}
                                            </span>
                                            <span class="mr-3">
                                                <i class="fas fa-flag mr-1"></i>
                                                {{ ucfirst($message->priority) }}
                                            </span>
                                            @if($message->expires_at)
                                                <span>
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Expire le {{ $message->expires_at->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

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
                <!-- Tableau synchronisé avec seller/orders -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Référence
                                </th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ville
                                </th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prix Total
                                </th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $order->reference }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->nom_client }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->ville }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ getStatusColor($order->status) }}">
                                            {{ getStatusLabel($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($order->prix_commande, 2) }} MAD
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

@php
function getStatusColor($status) {
    // Normaliser le statut pour la comparaison
    $normalizedStatus = strtolower(trim($status));
    $normalizedStatus = str_replace(['é', 'è', 'à', 'É', 'È', 'À'], ['e', 'e', 'a', 'e', 'e', 'a'], $normalizedStatus);

    switch ($normalizedStatus) {
        case 'en attente':
        case 'en attente':
            return 'bg-yellow-100 text-yellow-800';
        case 'confirme':
        case 'confirme':
            return 'bg-blue-100 text-blue-800';
        case 'en livraison':
        case 'en livraison':
            return 'bg-blue-100 text-blue-800';
        case 'livre':
        case 'livre':
            return 'bg-green-100 text-green-800';
        case 'annule':
        case 'annule':
            return 'bg-red-100 text-red-800';
        case 'retourne':
        case 'retourne':
            return 'bg-red-100 text-red-800';
        case 'pas de reponse':
        case 'pas de reponse':
            return 'bg-orange-100 text-orange-800';
        case 'en cours':
        case 'en cours':
            return 'bg-purple-100 text-purple-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getStatusLabel($status) {
    // Normaliser le statut pour la comparaison
    $normalizedStatus = strtolower(trim($status));
    $normalizedStatus = str_replace(['é', 'è', 'à', 'É', 'È', 'À'], ['e', 'e', 'a', 'e', 'e', 'a'], $normalizedStatus);

    switch ($normalizedStatus) {
        case 'en attente':
        case 'en attente':
            return 'En attente';
        case 'confirme':
        case 'confirme':
            return 'Confirmé';
        case 'en livraison':
        case 'en livraison':
            return 'En livraison';
        case 'livre':
        case 'livre':
            return 'Livré';
        case 'annule':
        case 'annule':
            return 'Annulé';
        case 'retourne':
        case 'retourne':
            return 'Retourné';
        case 'pas de reponse':
        case 'pas de reponse':
            return 'Pas de réponse';
        case 'en cours':
        case 'en cours':
            return 'En cours';
        default:
            return ucfirst($status);
    }
}
@endphp

@endsection
