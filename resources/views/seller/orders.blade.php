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

    @section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Mes Commandes</h1>
                    <a href="{{ route('seller.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nouvelle Commande
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filtres -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <form method="GET" action="{{ route('seller.orders.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les statuts</option>
                                <option value="en attente" @selected(request('status') == 'en attente')>En attente</option>
                                <option value="en cours" @selected(request('status') == 'en cours')>En cours</option>
                                <option value="livré" @selected(request('status') == 'livré')>Livré</option>
                                <option value="annulé" @selected(request('status') == 'annulé')>Annulé</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Référence, client, ville..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-search mr-2"></i>Filtrer
                            </button>
                            @if(request('status') || request('q'))
                                <a href="{{ route('seller.orders.index') }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-times mr-2"></i>Effacer
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Référence
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Client
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Ville
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Prix Total
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $order->reference }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $order->nom_client }}</div>
                                        <div class="text-sm leading-5 text-gray-500">{{ $order->numero_telephone_client }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $order->ville }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($order->status == 'en attente') bg-yellow-100 text-yellow-800
                                            @elseif($order->status == 'en cours') bg-blue-100 text-blue-800
                                            @elseif($order->status == 'livré') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900 font-medium">{{ number_format($order->prix_commande, 2) }} MAD</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('seller.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('seller.orders.edit', $order->id) }}" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('seller.orders.destroy', $order->id) }}" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Aucune commande trouvée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endsection
</body>
</html>
