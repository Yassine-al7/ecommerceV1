@extends('layouts.app')

@section('title', isset($order) ? 'Edit Commande' : 'Create Commande')

@section('content')
<div class="container mx-auto mt-5">
    <h1 class="text-3xl font-bold text-white mb-6">{{ isset($order) ? 'Modifier' : 'Créer' }} une commande</h1>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ isset($order) ? route('admin.orders.update', $order) : route('admin.orders.store') }}" class="space-y-4">
            @csrf
            @if(isset($order))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($order))
                <!-- Affichage de la référence existante en lecture seule -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Référence</label>
                    <input type="text" value="{{ $order->reference }}" 
                           class="mt-1 w-full border rounded px-3 py-2 bg-gray-50 text-gray-600" readonly>
                    <p class="text-xs text-gray-500 mt-1">La référence ne peut pas être modifiée</p>
                </div>
                @else
                <!-- Message pour nouvelle commande -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Référence</label>
                    <div class="mt-1 w-full border rounded px-3 py-2 bg-blue-50 text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Générée automatiquement lors de la création
                    </div>
                    <p class="text-xs text-blue-600 mt-1">Format: CMD-YYYYMMDD-XXXX</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom client</label>
                    <input type="text" name="nom_client" value="{{ old('nom_client', $order->nom_client ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ville</label>
                    <input type="text" name="ville" value="{{ old('ville', $order->ville ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" name="adresse_client" value="{{ old('adresse_client', $order->adresse_client ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                    <input type="text" name="numero_telephone_client" value="{{ old('numero_telephone_client', $order->numero_telephone_client ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Taille du produit</label>
                    <input type="text" name="taille_produit" value="{{ old('taille_produit', $order->taille_produit ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantité</label>
                    <input type="number" name="quantite_produit" value="{{ old('quantite_produit', $order->quantite_produit ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix produit</label>
                    <input type="number" step="0.01" name="prix_produit" value="{{ old('prix_produit', $order->prix_produit ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix commande</label>
                    <input type="number" step="0.01" name="prix_commande" value="{{ old('prix_commande', $order->prix_commande ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="status" class="mt-1 w-full border rounded px-3 py-2">
                        @php $s = old('status', $order->status ?? 'en attente'); @endphp
                        @foreach(['livré','retourné','pas de réponse','en attente','en livraison','refusé confirmé','non confirmé'] as $status)
                            <option value="{{ $status }}" @selected($s === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Commentaire</label>
                    <textarea name="commentaire" class="mt-1 w-full border rounded px-3 py-2">{{ old('commentaire', $order->commentaire ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Produits (JSON)</label>
                    <textarea name="produits" class="mt-1 w-full border rounded px-3 py-2" rows="4">{{ old('produits', isset($order) ? $order->produits : '[]') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Vendeur (ID utilisateur)</label>
                    <input type="number" name="seller_id" value="{{ old('seller_id', $order->seller_id ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="pt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">{{ isset($order) ? 'Mettre à jour' : 'Créer' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection


