@extends('layouts.app')

@section('title', isset($order) ? 'Modifier Commande' : 'Créer Commande')

@section('content')
<div class="container mx-auto mt-5">
    <h1 class="text-2xl font-bold mb-4">{{ isset($order) ? 'Modifier' : 'Créer' }} une commande</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ isset($order) ? route('seller.orders.update', $order->id) : route('seller.orders.store') }}" class="space-y-4">
            @csrf
            @if(isset($order))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm">Référence</label>
                    <input type="text" name="reference" value="{{ old('reference', $order->reference ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm">Nom client</label>
                    <input type="text" name="nom_client" value="{{ old('nom_client', $order->nom_client ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm">Ville</label>
                    <input type="text" name="ville" value="{{ old('ville', $order->ville ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm">Adresse</label>
                    <input type="text" name="adresse_client" value="{{ old('adresse_client', $order->adresse_client ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm">Téléphone</label>
                    <input type="text" name="numero_telephone_client" value="{{ old('numero_telephone_client', $order->numero_telephone_client ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm">Taille</label>
                    <input type="text" name="taille_produit" value="{{ old('taille_produit', $order->taille_produit ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm">Quantité</label>
                    <input type="number" name="quantite_produit" value="{{ old('quantite_produit', $order->quantite_produit ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm">Prix produit</label>
                    <input type="number" step="0.01" name="prix_produit" value="{{ old('prix_produit', $order->prix_produit ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm">Prix commande</label>
                    <input type="number" step="0.01" name="prix_commande" value="{{ old('prix_commande', $order->prix_commande ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm">Produit</label>
                    <select name="product_id" class="mt-1 w-full border rounded px-3 py-2" required>
                        @foreach(($products ?? []) as $p)
                            <option value="{{ $p->id }}" @selected(old('product_id', isset($order) && isset(json_decode($order->produits,true)[0]['product_id']) ? json_decode($order->produits,true)[0]['product_id'] : null) == $p->id)>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm">Commentaire</label>
                    <textarea name="commentaire" class="mt-1 w-full border rounded px-3 py-2">{{ old('commentaire', $order->commentaire ?? '') }}</textarea>
                </div>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">{{ isset($order) ? 'Mettre à jour' : 'Créer' }}</button>
        </form>
    </div>
</div>
@endsection


