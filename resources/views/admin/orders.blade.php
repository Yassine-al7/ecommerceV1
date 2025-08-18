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
    <div class="container mx-auto mt-5">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Commandes</h1>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendeur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->reference }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->nom_client }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->ville }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($order->prix_commande, 2) }} €</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->status }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($order->seller)->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Voir</a>
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="border rounded px-2 py-1 text-sm">
                                        <option value="livré" @selected($order->status==='livré')>livré</option>
                                        <option value="retourné" @selected($order->status==='retourné')>retourné</option>
                                        <option value="pas de réponse" @selected($order->status==='pas de réponse')>pas de réponse</option>
                                        <option value="en attente" @selected($order->status==='en attente')>en attente</option>
                                        <option value="en livraison" @selected($order->status==='en livraison')>en livraison</option>
                                        <option value="refusé confirmé" @selected($order->status==='refusé confirmé')>refusé confirmé</option>
                                        <option value="non confirmé" @selected($order->status==='non confirmé')>non confirmé</option>
                                    </select>
                                    <button class="ml-2 text-green-600">Mettre à jour</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
    @endsection
</body>
</html>


