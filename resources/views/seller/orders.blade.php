<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('title', 'Mes Commandes')

    @section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Mes Commandes</h1>
                <p class="text-gray-600">Gérez vos commandes et suivez leur progression</p>
            </div>

            <!-- Cartes de Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Total Commandes -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-400">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- En Attente -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-400">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-clock text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">En Attente</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['en_attente'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Confirmé -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-400">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Confirmé</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['confirme'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Livré -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-400">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-truck text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Livré</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['livre'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Problématique -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-400">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Problématique</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['problematique'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pas de Réponse -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-orange-400">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                            <i class="fas fa-phone text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pas de Réponse</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pas_de_reponse'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton Nouvelle Commande -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Gestion des Commandes</h2>
                        <p class="text-sm text-gray-600">Créez et gérez vos commandes</p>
                    </div>
                    <a href="{{ route('seller.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nouvelle Commande
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tableau des Commandes -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Liste des Commandes</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Référence
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ville
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prix Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $order->reference }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->nom_client }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->ville }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ getStatusColor($order->status) }}">
                                            {{ getStatusLabel($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($order->prix_commande, 2) }} MAD
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('seller.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('seller.orders.edit', $order->id) }}" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Aucune commande trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $orders->links() }}
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
</body>
</html>
