@extends('layouts.app')

@section('title', 'Seller Dashboard')

@section('content')
<div class="glass-effect rounded-2xl shadow-2xl p-8 w-full">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-tachometer-alt text-3xl text-blue-600"></i>
        </div>
        <h1 class="text-4xl font-bold text-white mb-2">Seller Dashboard</h1>
        <p class="text-blue-100 text-lg">Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Quick Stats (réelles) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <i class="fas fa-box text-4xl text-blue-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">{{ $totalAssignedProducts }}</h3>
            <p class="text-blue-100">Produits assignés</p>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <i class="fas fa-warehouse text-4xl text-indigo-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">{{ $totalAdminProducts }}</h3>
            <p class="text-blue-100">Produits (catalogue)</p>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <i class="fas fa-shopping-cart text-4xl text-green-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">{{ $totalSellerOrders }}</h3>
            <p class="text-blue-100">Commandes</p>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <i class="fas fa-dollar-sign text-4xl text-yellow-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">{{ number_format($sellerProfit, 0) }} MAD</h3>
            <p class="text-blue-100">Bénéfice (livrées)</p>
        </div>
    </div>

    <!-- Main Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <a href="{{ route('seller.products.index') }}"
           class="bg-white/10 rounded-xl p-8 text-center hover:bg-white/20 transition duration-200 card-hover group">
            <i class="fas fa-box text-5xl text-white mb-6 group-hover:text-blue-200 transition duration-200"></i>
            <h3 class="text-2xl font-bold text-white mb-3">Manage Products</h3>
            <p class="text-blue-100 mb-4">Add, edit, and organize your product inventory</p>
            <div class="bg-blue-500 text-white px-4 py-2 rounded-lg inline-block">
                <i class="fas fa-arrow-right mr-2"></i>Go to Products
            </div>
        </a>

        <a href="{{ route('seller.orders.index') }}" class="bg-white/10 rounded-xl p-8 text-center hover:bg-white/20 transition duration-200 card-hover group">
            <i class="fas fa-shopping-cart text-5xl text-white mb-6 group-hover:text-green-200 transition duration-200"></i>
            <h3 class="text-2xl font-bold text-white mb-3">Mes Commandes</h3>
            <p class="text-blue-100 mb-4">Suivre et gérer vos commandes clients</p>
            <div class="bg-green-500 text-white px-4 py-2 rounded-lg inline-block">
                <i class="fas fa-arrow-right mr-2"></i>Voir les commandes
            </div>
        </a>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white/5 rounded-xl p-6 mb-8">
        <h3 class="text-xl font-bold text-white mb-4">
            <i class="fas fa-clock mr-2"></i>Activité récente
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Dernières commandes -->
            <div class="bg-white/5 rounded-lg p-4">
                <h4 class="text-white font-semibold mb-3"><i class="fas fa-receipt mr-2"></i>Dernières commandes</h4>
                <div class="space-y-2">
                    @forelse($recentOrders as $o)
                        <div class="flex items-center justify-between p-3 bg-white/10 rounded">
                            <div>
                                <p class="text-blue-100 text-sm">Ref: <span class="font-semibold">{{ $o->reference }}</span></p>
                                <p class="text-blue-200 text-xs">Client: {{ $o->nom_client }} • {{ $o->ville }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($o->status=='en attente') bg-yellow-100 text-yellow-800 @elseif($o->status=='livré') bg-green-100 text-green-800 @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($o->status) }}
                                </span>
                                <p class="text-blue-300 text-xs mt-1">{{ $o->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-blue-200 text-sm italic">Aucune commande récente</p>
                    @endforelse
                </div>
            </div>

            <!-- Dernières assignations produits -->
            <div class="bg-white/5 rounded-lg p-4">
                <h4 class="text-white font-semibold mb-3"><i class="fas fa-box mr-2"></i>Derniers produits assignés</h4>
                <div class="space-y-2">
                    @forelse($recentAssignments as $a)
                        <div class="p-3 bg-white/10 rounded">
                            <div class="flex items-center justify-between">
                                <p class="text-blue-100 font-medium">{{ $a->product_name }}</p>
                                <p class="text-blue-300 text-xs">{{ \Carbon\Carbon::parse($a->assigned_at)->diffForHumans() }}</p>
                            </div>
                            @if($a->product_sizes)
                                @php
                                    $sizes = is_string($a->product_sizes) ? json_decode($a->product_sizes, true) : (array)$a->product_sizes;
                                    $sizes = is_array($sizes) ? $sizes : [];
                                @endphp
                                @if(!empty($sizes))
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($sizes as $sz)
                                            <span class="px-2 py-0.5 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200">{{ $sz }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-blue-200 text-sm italic">Aucune assignation récente</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
