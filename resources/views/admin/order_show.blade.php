<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande - Détails</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Détails de la commande {{ $order->reference }}</h1>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">← Retour</a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Client</p>
                    <p class="text-lg text-gray-900">{{ $order->nom_client }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ville</p>
                    <p class="text-lg text-gray-900">{{ $order->ville }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Adresse</p>
                    <p class="text-lg text-gray-900">{{ $order->adresse_client }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Téléphone</p>
                    <p class="text-lg text-gray-900">{{ $order->numero_telephone_client }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Prix commande</p>
                    <p class="text-lg text-gray-900">{{ number_format($order->prix_commande, 2) }} €</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Statut</p>
                    <p class="text-lg text-gray-900">{{ $order->status }}</p>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-2">Produits</p>
                <pre class="bg-gray-50 p-4 rounded border text-sm">{{ json_encode(json_decode($order->produits, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
    @endsection
</body>
</html>


