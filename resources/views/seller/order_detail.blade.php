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
        <div><strong>Prix commande:</strong> {{ number_format((float)$order->prix_commande, 2) }} €</div>
        <div><strong>Produits:</strong>
            <div class="bg-gray-50 p-4 rounded border mt-2">
                @php
                    $decodedProducts = json_decode($order->produits, true);
                @endphp

                @if(is_array($decodedProducts) && count($decodedProducts) > 0)
                    <div class="space-y-3">
                        @foreach($decodedProducts as $produit)
                            @if(is_array($produit) && isset($produit['product_id']) && isset($produit['qty']))
                                @php
                                    $product = App\Models\Product::find($produit['product_id']);
                                    $productName = $product ? $product->name : 'Produit non trouvé';
                                    $category = $product && $product->category ? $product->category->name : 'Catégorie inconnue';
                                    $price = $product ? $product->prix_vente : 0;
                                    $totalPrice = $price * $produit['qty'];
                                @endphp

                                <div class="flex items-center justify-between bg-white p-3 rounded border">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $productName }}</h4>
                                        <p class="text-sm text-gray-500">Catégorie: {{ $category }}</p>
                                        <p class="text-sm text-gray-500">Prix unitaire: {{ number_format((float)$price, 0) }} MAD</p>
                                        <p class="text-sm text-gray-500">Taille: {{ $order->taille_produit }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">Quantité: {{ $produit['qty'] }}</p>
                                        <p class="text-sm text-green-600 font-medium">Total: {{ number_format((float)$totalPrice, 0) }} MAD</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">Aucun produit trouvé ou format invalide</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


