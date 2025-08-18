@extends('layouts.app')

@section('title', isset($order) ? 'Modifier Commande' : 'Créer Commande')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ isset($order) ? 'Modifier' : 'Créer' }} une commande</h1>
            <p class="text-gray-600 mt-2">Remplissez les informations pour {{ isset($order) ? 'modifier' : 'créer' }} une nouvelle commande</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ isset($order) ? route('seller.orders.update', $order->id) : route('seller.orders.store') }}" class="space-y-6">
                @csrf
                @if(isset($order))
                    @method('PUT')
                @endif

                <!-- Informations client -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Informations client
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Référence *</label>
                            <input type="text" name="reference" value="{{ old('reference', $order->reference ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom client *</label>
                            <input type="text" name="nom_client" value="{{ old('nom_client', $order->nom_client ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                            <input type="text" name="ville" value="{{ old('ville', $order->ville ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Adresse *</label>
                            <input type="text" name="adresse_client" value="{{ old('adresse_client', $order->adresse_client ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                            <input type="text" name="numero_telephone_client" value="{{ old('numero_telephone_client', $order->numero_telephone_client ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                </div>

                <!-- Informations produit -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-box mr-2 text-green-600"></i>
                        Informations produit
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Produit *</label>
                            <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Sélectionnez un produit</option>
                                @foreach(($products ?? []) as $p)
                                    <option value="{{ $p->id }}" @selected(old('product_id', isset($order) && isset(json_decode($order->produits,true)[0]['product_id']) ? json_decode($order->produits,true)[0]['product_id'] : null) == $p->id)>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Taille *</label>
                            <input type="text" name="taille_produit" value="{{ old('taille_produit', $order->taille_produit ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantité *</label>
                            <input type="number" name="quantite_produit" value="{{ old('quantite_produit', $order->quantite_produit ?? '1') }}" min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                </div>

                <!-- Commentaire -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-comment mr-2 text-purple-600"></i>
                        Commentaire
                    </h3>
                    <textarea name="commentaire" rows="3" placeholder="Ajoutez un commentaire optionnel..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('commentaire', $order->commentaire ?? '') }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('seller.orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>{{ isset($order) ? 'Mettre à jour' : 'Créer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


