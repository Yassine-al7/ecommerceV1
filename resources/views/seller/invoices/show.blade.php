@extends('layouts.app')

@section('title', 'Facture - ' . $order->reference)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Facture {{ $order->reference }}</h1>
                    <p class="text-gray-600">Détails de la commande et statut de paiement</p>
                </div>
                <a href="{{ route('seller.invoices.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>

        <!-- Informations de la Commande -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations de la Commande</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Client</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $order->nom_client }}</p>
                    <p class="text-gray-600">{{ $order->adresse_client }}</p>
                    <p class="text-gray-600">{{ $order->numero_telephone_client }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-600 mb-2">Livraison</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $order->ville }}</p>
                    <p class="text-gray-600">Date: {{ \Carbon\Carbon::parse($order->updated_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Produits de la Commande -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Produits Commandés</h2>
            @if($order->produits)
                @php $produits = json_decode($order->produits, true); @endphp
                <div class="space-y-4">
                    @foreach($produits as $produit)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $produit['name'] ?? 'Produit' }}</p>
                                    <p class="text-sm text-gray-600">
                                        Taille: {{ $produit['taille'] ?? 'N/A' }} |
                                        Quantité: {{ $produit['qty'] ?? 0 }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ number_format($produit['prix_vente_client'] ?? 0, 2) }} MAD</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Aucun produit trouvé</p>
            @endif
        </div>

        <!-- Résumé Financier -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Résumé Financier</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Prix Total Commande:</span>
                    <span class="font-semibold text-gray-900">{{ number_format($order->prix_commande, 2) }} MAD</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Marge Bénéfice:</span>
                    <span class="font-semibold text-emerald-600">{{ number_format($order->marge_benefice, 2) }} MAD</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600">Statut Paiement:</span>
                    @if($order->facturation_status == 'payé')
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Payé
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>En attente de paiement
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Note de Synchronisation -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div>
                    <h3 class="text-sm font-medium text-blue-800 mb-1">Synchronisation avec l'Admin</h3>
                    <p class="text-sm text-blue-700">
                        Le statut de paiement est géré par l'administrateur.
                        Toute modification du statut sera automatiquement reflétée ici.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
