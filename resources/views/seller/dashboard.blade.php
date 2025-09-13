@extends('layouts.app')

@section('title', __('seller.title'))

@section('content')
<div class="container mx-auto px-4 py-4 md:py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 md:p-6 text-blue-900 shadow-sm text-center" dir="rtl">
                <h1 class="text-2xl md:text-3xl font-extrabold mb-1">{{ __('seller.title') }}</h1>
                <p class="text-sm md:text-base leading-relaxed">{{ __('seller.welcome', ['name' => auth()->user()->name]) }}</p>
            </div>
        </div>

        <!-- Messages Admin -->
        @if($adminMessages->count() > 0)
            <div class="mb-6 md:mb-8">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 md:p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 md:p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-bullhorn text-lg md:text-xl"></i>
                        </div>
                        <h2 class="ml-3 text-lg md:text-xl font-semibold text-blue-800">{{ __('seller.admin_messages.title') }}</h2>
                    </div>

                    <div class="space-y-3">
                        @foreach($adminMessages as $message)
                            <div class="bg-white rounded-lg border-l-4 {{ $message->getPriorityClass() }} p-3 md:p-4 shadow-sm">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <i class="{{ $message->getIcon() }} mr-2 text-{{ $message->type === 'celebration' ? 'purple' : ($message->type === 'success' ? 'green' : ($message->type === 'warning' ? 'yellow' : ($message->type === 'error' ? 'red' : 'blue'))) }}-600"></i>
                                            <h3 class="font-semibold text-gray-800">{{ $message->title }}</h3>
                                            @if($message->isUrgent())
                                                <span class="ml-2 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">{{ __('seller.admin_messages.urgent') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">{{ $message->message }}</p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="mr-3">
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ __('seller.admin_messages.type') }}: {{ ucfirst($message->type) }}
                                            </span>
                                            <span class="mr-3">
                                                <i class="fas fa-flag mr-1"></i>
                                                {{ __('seller.admin_messages.priority') }}: {{ ucfirst($message->priority) }}
                                            </span>
                                            @if($message->expires_at)
                                                <span>
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ __('seller.admin_messages.expires_at', ['date' => $message->expires_at->format('d/m/Y H:i')]) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Statistiques Principales -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <!-- Produits Assignés -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-box text-lg md:text-2xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">{{ __('seller.stats.assigned_products') }}</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $totalAssignedProducts }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Commandes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-shopping-cart text-lg md:text-2xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">{{ __('seller.stats.total_orders') }}</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ $totalSellerOrders }}</p>
                    </div>
                </div>
            </div>

            <!-- Chiffre d'Affaires -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-2 md:p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-coins text-lg md:text-2xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-xs md:text-sm font-medium text-gray-600">{{ __('seller.stats.revenue') }}</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900">{{ number_format((float)$totalRevenue, 0) }} MAD</p>
                    </div>
                </div>
            </div>


        </div>

        <!-- Statistiques Commandes -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller.stats.pending') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ordersEnAttente }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller.statuses.confirmed') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ordersConfirme }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-shipping-fast text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller.statuses.shipping') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ordersExpedition }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller.stats.delivered') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ordersLivrees }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller.stats.cancelled') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ordersCancelled }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques Paiements -->
        <div class="grid grid-cols-2 gap-3 md:gap-6 mb-6 md:mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller.stats.paid_profits') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format((float)$totalPaid, 0) }} MAD</p>
                        <p class="text-xs text-gray-500">{{ __('seller.stats.paid_orders') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-3 md:ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('seller.stats.pending_profits') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format((float)$totalPending, 0) }} MAD</p>
                        <p class="text-xs text-gray-500">{{ __('seller.stats.unpaid_orders') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
            <a href="{{ route('seller.products.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 md:p-6 text-center transition-colors group">
                <i class="fas fa-box text-2xl md:text-3xl mb-2 md:mb-3 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-base md:text-lg font-semibold">{{ __('seller.quick.my_products') }}</h3>
                <p class="text-xs md:text-sm opacity-90">{{ __('seller.quick.my_products_hint') }}</p>
            </a>

            <a href="{{ route('seller.orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-4 md:p-6 text-center transition-colors group">
                <i class="fas fa-plus text-2xl md:text-3xl mb-2 md:mb-3 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-base md:text-lg font-semibold">{{ __('seller.quick.new_order') }}</h3>
                <p class="text-xs md:text-sm opacity-90">{{ __('seller.quick.new_order_hint') }}</p>
            </a>

            <a href="{{ route('seller.orders.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-4 md:p-6 text-center transition-colors group">
                <i class="fas fa-list text-2xl md:text-3xl mb-2 md:mb-3 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-lg font-semibold">{{ __('seller.quick.my_orders') }}</h3>
                <p class="text-xs md:text-sm opacity-90">{{ __('seller.quick.my_orders_hint') }}</p>
            </a>

            <a href="{{ route('seller.invoices.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white rounded-lg p-4 md:p-6 text-center transition-colors group">
                <i class="fas fa-file-invoice text-2xl md:text-3xl mb-2 md:mb-3 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-lg font-semibold">{{ __('seller.quick.invoices') }}</h3>
                <p class="text-xs md:text-sm opacity-90">{{ __('seller.quick.invoices_hint') }}</p>
            </a>
        </div>

        <!-- Commandes Récentes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h2 class="text-base md:text-lg font-semibold text-gray-800">{{ __('seller.recent_orders.title') }}</h2>
                    <a href="{{ route('seller.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium self-start sm:self-center">
                        {{ __('seller.recent_orders.view_all') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            @if($recentOrders->count() > 0)
                <!-- Tableau synchronisé avec seller/orders -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('seller.recent_orders.headers.reference') }}</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('seller.recent_orders.headers.client') }}</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('seller.recent_orders.headers.city') }}</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('seller.recent_orders.headers.status') }}</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('seller.recent_orders.headers.total_price') }}</th>
                                <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('seller.recent_orders.headers.date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $order->reference }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->nom_client }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->ville }}
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ getStatusColor($order->status) }}">
                                            {{ getStatusLabel($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format((float)$order->prix_commande, 2) }} MAD
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 md:py-12">
                    <div class="text-gray-400 mb-3 md:mb-4">
                        <i class="fas fa-shopping-cart text-4xl md:text-6xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-medium text-gray-800 mb-2">{{ __('seller.recent_orders.empty_title') }}</h3>
                    <p class="text-sm md:text-base text-gray-600 mb-3 md:mb-4">{{ __('seller.recent_orders.empty_subtitle') }}</p>
                    <a href="{{ route('seller.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-2 rounded-lg font-medium transition-colors text-sm md:text-base">
                        <i class="fas fa-plus mr-2"></i>{{ __('seller.recent_orders.empty_cta') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- getStatusColor() and getStatusLabel() functions are now available globally --}}

@endsection
