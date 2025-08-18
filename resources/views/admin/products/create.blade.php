@extends('layouts.app')

@section('title', 'Créer Produit')

@section('content')
<div class="container mx-auto mt-5">
    <h1 class="text-3xl font-bold text-white mb-6">Créer un produit</h1>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="name" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Couleur</label>
                    <input type="text" name="couleur" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tailles (séparées par virgules)</label>
                    <input type="text" name="tailles" class="mt-1 w-full border rounded px-3 py-2" placeholder="S,M,L">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Image (URL)</label>
                    <input type="text" name="image" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantité stock</label>
                    <input type="number" name="quantite_stock" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="categorie_id" class="mt-1 w-full border rounded px-3 py-2" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix admin (€)</label>
                    <input type="number" step="0.01" name="prix_admin" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix vente (€)</label>
                    <input type="number" step="0.01" name="prix_vente" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Créer</button>
        </form>
    </div>
</div>
@endsection


