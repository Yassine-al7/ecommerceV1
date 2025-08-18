<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sellers</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Gestion des Vendeurs</h1>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Retour au Dashboard
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Liste des Vendeurs</h2>
            </div>

            @if($sellers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom
                                </th>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date d'inscription
                                </th>
                                <th class="px-6 py-3 border-b border-gray-300 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sellers as $seller)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $seller->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $seller->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $seller->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900 mr-3">Voir détails</button>
                                        <button class="text-yellow-600 hover:text-yellow-900 mr-3">Modifier</button>
                                        <button class="text-red-600 hover:text-red-900">Désactiver</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500 text-lg">Aucun vendeur trouvé.</p>
                    <p class="text-gray-400 mt-2">Les vendeurs apparaîtront ici une fois qu'ils s'inscriront.</p>
                </div>
            @endif
        </div>
    </div>
    @endsection
</body>
</html>
