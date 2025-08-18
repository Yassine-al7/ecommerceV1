@extends('layouts.app')

@section('title', 'Détails Commande')

@section('content')
<div class="container mx-auto mt-5">
    <h1 class="text-2xl font-bold mb-4">Commande {{ $order->reference }}</h1>

    <div class="bg-white rounded-lg shadow p-6 space-y-2">
        <div><strong>Client:</strong> {{ $order->nom_client }}</div>
        <div><strong>Ville:</strong> {{ $order->ville }}</div>
        <div><strong>Adresse:</strong> {{ $order->adresse_client }}</div>
        <div><strong>Téléphone:</strong> {{ $order->numero_telephone_client }}</div>
        <div><strong>Statut:</strong> {{ $order->status }}</div>
        <div><strong>Prix commande:</strong> {{ number_format($order->prix_commande, 2) }} €</div>
        <div><strong>Produits:</strong>
            <pre class="bg-gray-50 p-3 rounded border text-sm">{{ json_encode(json_decode($order->produits, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</div>
@endsection


