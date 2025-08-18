<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Commandes</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Gestion des Commandes</h1>
                    <a href="{{ route('admin.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nouvelle Commande
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

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
                                    Vendeur
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
                                        <div class="text-sm leading-5 text-gray-900">{{ $order->seller->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $order->ville }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500">
                                                <option value="en attente" @selected($order->status == 'en attente')>En attente</option>
                                                <option value="en cours" @selected($order->status == 'en cours')>En cours</option>
                                                <option value="livré" @selected($order->status == 'livré')>Livré</option>
                                                <option value="annulé" @selected($order->status == 'annulé')>Annulé</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900 font-medium">{{ number_format($order->prix_commande, 2) }} MAD</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande?')">
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
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
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


