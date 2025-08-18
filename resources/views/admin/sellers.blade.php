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
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Gestion des Vendeurs</h1>
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
                                    Nom complet
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Magasin
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Téléphone
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Date d'inscription
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($sellers as $seller)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900 font-medium">{{ $seller->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $seller->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $seller->store_name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-900">{{ $seller->numero_telephone ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <div class="text-sm leading-5 text-gray-500">{{ $seller->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($seller->email_verified_at) bg-green-100 text-green-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            @if($seller->email_verified_at)
                                                Vérifié
                                            @else
                                                En attente
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Aucun vendeur trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 text-center text-sm text-gray-500">
                    <p>Total des vendeurs : {{ $sellers->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    @endsection
</body>
</html>
