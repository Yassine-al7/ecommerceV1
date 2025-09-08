@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-6 md:py-10">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('seller.products.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100 hover:border-blue-300 text-sm font-medium transition-colors">
                <i class="fas fa-arrow-right rotate-180"></i>
                <span>{{ __('seller.quick.my_products') }}</span>
            </a>

            <div class="hidden md:flex items-center gap-2 text-xs text-gray-500">
                <span>{{ $product->category->name ?? '-' }}</span>
                <span>•</span>
                <span>#{{ $product->id }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="aspect-square bg-gray-50">
                        @php
                            $src = trim($product->image ?? '', '/');
                            if (preg_match('#^https?://#i', $src)) {
                                $imageUrl = $src;
                            } elseif ($src) {
                                // Pour Hostinger: utiliser directement le chemin sans asset()
                                $imageUrl = '/' . ltrim($product->image, '/');
                            } else {
                                $imageUrl = null;
                            }
                        @endphp
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                            <div class="hidden h-full w-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @else
                            <div class="h-full w-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        <h1 class="text-xl font-bold text-gray-900 mb-1">{{ $product->name }}</h1>
                        <p class="text-sm text-gray-500">{{ $product->category->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6">
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ __('seller.products.details.admin_price') }}</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format((float) ($product->prix_admin_moyen ?? 0), 2) }} {{ __('seller.products.details.currency_mad') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ __('seller.stats.revenue') }}</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format((float) ($product->prix_vente ?? 0), 2) }} {{ __('seller.products.details.currency_mad') }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ __('seller.products.details.stock_by_color') }}</p>
                            <p class="text-lg font-bold text-gray-900">{{ $product->total_stock ?? $product->quantite_stock ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-600 mb-2">{{ __('seller.products.details.colors') }}</h2>
                            @if(!empty($colors))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($colors as $c)
                                        @php $label = is_array($c) ? ($c['name'] ?? '') : $c; @endphp
                                        <span class="px-2 py-1 text-xs rounded-full border border-gray-200 bg-gray-50 text-gray-700">{{ $label }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">-</p>
                            @endif
                        </div>

                        <div>
                            <h2 class="text-sm font-semibold text-gray-600 mb-2">{{ __('seller.products.details.sizes') }}</h2>
                            @if(!empty($sizes))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($sizes as $s)
                                        <span class="px-2 py-1 text-xs rounded border border-gray-200 bg-gray-50 text-gray-700">{{ is_string($s) ? trim($s, "[]\"'") : $s }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">-</p>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <h2 class="text-sm font-semibold text-gray-600 mb-2">{{ __('seller.products.details.stock_by_color') }}</h2>
                            @if(!empty($stockByColor))
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($stockByColor as $sc)
                                        <div class="flex items-center justify-between px-3 py-2 rounded border border-gray-200">
                                            <span class="text-sm text-gray-700">{{ $sc['name'] ?? '-' }}</span>
                                            <span class="text-sm font-semibold text-gray-900">{{ (int)($sc['quantity'] ?? 0) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">-</p>
                            @endif
                        </div>

                        <div class="md:col-span-2">
                            <h2 class="text-sm font-semibold text-gray-600 mb-2">{{ __('seller.products.details.admin_price') }}</h2>
                            @php $adminPrices = $product->prix_admin_array ?? []; @endphp
                            @if(!empty($adminPrices))
                                <div class="flex flex-wrap items-center gap-2">
                                    @foreach($adminPrices as $pa)
                                        <span class="px-2 py-1 rounded-full border border-gray-200 bg-gray-50 text-sm text-gray-800">{{ number_format((float)$pa, 2) }} {{ __('seller.products.details.currency_mad') }}</span>
                                    @endforeach
                                    <span class="px-2 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs">avg: {{ number_format((float)($product->prix_admin_moyen ?? 0), 2) }} {{ __('seller.products.details.currency_mad') }}</span>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">—</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


