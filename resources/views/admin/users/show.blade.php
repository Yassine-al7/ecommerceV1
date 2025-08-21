@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Détails du Vendeur</h1>
                    <p class="text-gray-600 mt-1">Informations complètes de {{ $user->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Modifier
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Carte d'identité -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Informations d'Identité</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nom complet</label>
                                <p class="text-lg font-medium text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Adresse email</label>
                                <p class="text-lg font-medium text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Rôle</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-store mr-1"></i>
                                    Vendeur
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Statut du compte</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->is_active ?? true ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas {{ $user->is_active ?? true ? 'fa-check-circle' : 'fa-ban' }} mr-1"></i>
                                    {{ $user->is_active ?? true ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations du magasin -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Informations du Magasin</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nom du magasin</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $user->store_name ?: 'Non spécifié' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Numéro de téléphone</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $user->phone ?: 'Non spécifié' }}
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Adresse</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $user->address ?: 'Non spécifiée' }}
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">RIB</label>
                                <p class="text-lg font-mono text-gray-900">
                                    {{ $user->rib ? Str::mask($user->rib, '*', 4, -4) : 'Non spécifié' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques du vendeur -->
                @if($sellerStats)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Statistiques du Vendeur</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $sellerStats['total_orders'] }}</div>
                                <div class="text-sm text-gray-500">Total Commandes</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $sellerStats['pending_orders'] }}</div>
                                <div class="text-sm text-gray-500">En Attente</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $sellerStats['delivered_orders'] }}</div>
                                <div class="text-sm text-gray-500">Livrées</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $sellerStats['cancelled_orders'] }}</div>
                                <div class="text-sm text-gray-500">Annulées</div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-600">{{ number_format($sellerStats['total_revenue'], 0) }} MAD</div>
                                    <div class="text-sm text-gray-500">Chiffre d'Affaires</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600">{{ number_format($sellerStats['total_profit'], 0) }} MAD</div>
                                    <div class="text-sm text-gray-500">Bénéfices Totaux</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-purple-600">{{ $sellerStats['assigned_products'] }}</div>
                                    <div class="text-sm text-gray-500">Produits Assignés</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar avec actions et informations -->
            <div class="space-y-6">
                <!-- Actions rapides -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Actions Rapides</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2 rounded-lg transition-colors
                                            {{ $user->is_active ?? true
                                                ? 'bg-orange-100 text-orange-700 hover:bg-orange-200'
                                                : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                    <i class="fas {{ $user->is_active ?? true ? 'fa-ban' : 'fa-check' }} mr-2"></i>
                                    {{ $user->is_active ?? true ? 'Désactiver' : 'Activer' }} le compte
                                </button>
                            </form>

                            <form method="POST"
                                  action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce vendeur ?')"
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Supprimer le vendeur
                                </button>
                            </form>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle text-blue-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600">Vous ne pouvez pas modifier votre propre compte</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations du compte -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Informations du Compte</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Date de création</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $user->created_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dernière modification</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $user->updated_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email vérifié</label>
                            <p class="text-sm font-medium text-gray-900">
                                @if($user->email_verified_at)
                                    <span class="text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i>Oui
                                    </span>
                                @else
                                    <span class="text-red-600">
                                        <i class="fas fa-times-circle mr-1"></i>Non
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Note informative -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-400 mr-2 mt-1"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium">Informations importantes :</p>
                            <ul class="mt-1 list-disc list-inside text-xs">
                                <li>Les statistiques sont basées sur les commandes existantes</li>
                                <li>Le RIB est masqué pour des raisons de sécurité</li>
                                <li>Seuls les vendeurs actifs peuvent recevoir des commandes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
