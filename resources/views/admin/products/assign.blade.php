@extends('layouts.app')

@section('title', 'Assigner Produit')

@section('content')
<div class="container mx-auto mt-5">
    <h1 class="text-3xl font-bold text-white mb-6">Assigner "{{ $product->name }}" à un vendeur</h1>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('admin.products.assign.store', $product) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Vendeur</label>
                <select name="user_id" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="">-- Choisir un vendeur --</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}">{{ $seller->name }} ({{ $seller->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix admin (€)</label>
                    <input type="number" step="0.01" name="prix_admin" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Prix vente (€)</label>
                    <input type="number" step="0.01" name="prix_vente" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="visible" value="1" checked>
                <label>Visible pour le vendeur</label>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Assigner</button>
        </form>
    </div>
</div>
@endsection


