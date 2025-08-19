<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Tableau de bord Admin</h1>
                <p class="text-gray-600">Vue d'ensemble de l'activité et accès rapide aux sections clés</p>
            </div>

            <!-- Statistiques rapides -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                    <i class="fas fa-receipt text-3xl text-blue-600 mb-3"></i>
                    <h3 class="text-2xl font-bold text-blue-900">{{ $totalOrders }}</h3>
                    <p class="text-blue-700">Commandes totales</p>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
                    <i class="fas fa-users text-3xl text-green-600 mb-3"></i>
                    <h3 class="text-2xl font-bold text-green-900">{{ $totalSellers }}</h3>
                    <p class="text-green-700">Vendeurs actifs</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <i class="fas fa-clock text-3xl text-yellow-600 mb-3"></i>
                    <h3 class="text-2xl font-bold text-yellow-900">{{ \App\Models\Order::where('status', 'en attente')->count() }}</h3>
                    <p class="text-yellow-700">En attente</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 text-center">
                    <i class="fas fa-check-circle text-3xl text-purple-600 mb-3"></i>
                    <h3 class="text-2xl font-bold text-purple-900">{{ \App\Models\Order::where('status', 'livré')->count() }}</h3>
                    <p class="text-purple-700">Livrées</p>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('admin.products.index') }}" class="bg-white rounded-xl p-6 border shadow-sm hover:shadow-md transition card-hover">
                    <i class="fas fa-box text-3xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-1">Produits</h3>
                    <p class="text-gray-500">Créer, modifier et assigner des produits</p>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-xl p-6 border shadow-sm hover:shadow-md transition card-hover">
                    <i class="fas fa-list-check text-3xl text-green-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-1">Commandes</h3>
                    <p class="text-gray-500">Suivre et gérer les commandes</p>
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="bg-white rounded-xl p-6 border shadow-sm hover:shadow-md transition card-hover">
                    <i class="fas fa-file-invoice text-3xl text-purple-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-1">Facturation</h3>
                    <p class="text-gray-500">Synthèse des ventes et paiements vendeurs</p>
                </a>
            </div>
        </div>
    </div>
    @endsection
</body>
</html>
