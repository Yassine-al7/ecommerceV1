@extends('layouts.app')

@section('title', 'Modifier Produit')

@section('content')
<div class="container mx-auto mt-5">
    <h1 class="text-3xl font-bold text-white mb-6">Modifier le produit</h1>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Couleur</label>
                    <input type="text" name="couleur" value="{{ old('couleur', $product->couleur) }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tailles (séparées par virgules)</label>
                    <input type="text" name="tailles" value="{{ old('tailles', implode(',', json_decode($product->tailles ?? '[]', true))) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Image (URL)</label>
                    <input type="text" name="image" value="{{ old('image', $product->image) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantité stock</label>
                    <input type="number" name="quantite_stock" value="{{ old('quantite_stock', $product->quantite_stock) }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="categorie_id" class="mt-1 w-full border rounded px-3 py-2" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($cat->id == $product->categorie_id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix admin (€)</label>
                    <input type="number" step="0.01" name="prix_admin" value="{{ old('prix_admin', $product->prix_admin) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix vente (€)</label>
                    <input type="number" step="0.01" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Mettre à jour</button>
        </form>
    </div>
</div>
@endsection


