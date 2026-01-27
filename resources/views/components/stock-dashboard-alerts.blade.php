@if(isset($stockAlerts) && $stockAlerts && $stockAlerts['total_alerts'] > 0)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        ⚠️ Alertes de Stock - {{ $stockAlerts['total_alerts'] }} produit(s) à vérifier
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Certains produits ont un stock faible ou sont en rupture. Vérifiez immédiatement.</p>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('admin.stock.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    Vérifier le Stock
                </a>
            </div>
        </div>
        
        @if(isset($stockAlerts['out_of_stock']) && count($stockAlerts['out_of_stock']) > 0)
            <div class="mt-4">
                <h4 class="text-sm font-medium text-red-800 mb-2">
                    🔴 Produits en Rupture ({{ count($stockAlerts['out_of_stock']) }})
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($stockAlerts['out_of_stock']->take(6) as $product)
                        <div class="bg-red-100 rounded px-2 py-1 text-xs text-red-800">
                            <span class="font-medium">{{ $product->name }}</span>
                            @if($product->category)
                                <span class="text-red-600">({{ $product->category->name }})</span>
                            @endif
                        </div>
                    @endforeach
                    @if(count($stockAlerts['out_of_stock']) > 6)
                        <div class="bg-red-100 rounded px-2 py-1 text-xs text-red-800">
                            +{{ count($stockAlerts['out_of_stock']) - 6 }} autre(s)...
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        @if(isset($stockAlerts['low_stock']) && count($stockAlerts['low_stock']) > 0)
            <div class="mt-4">
                <h4 class="text-sm font-medium text-orange-800 mb-2">
                    🟡 Stock Faible ({{ count($stockAlerts['low_stock']) }})
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($stockAlerts['low_stock']->take(6) as $product)
                        <div class="bg-orange-100 rounded px-2 py-1 text-xs text-orange-800">
                            <span class="font-medium">{{ $product->name }}</span>
                            <span class="text-orange-600">(Stock: {{ $product->quantite_stock }})</span>
                        </div>
                    @endforeach
                    @if(count($stockAlerts['low_stock']) > 6)
                        <div class="bg-orange-100 rounded px-2 py-1 text-xs text-orange-800">
                            +{{ count($stockAlerts['low_stock']) - 6 }} autre(s)...
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        @if(isset($stockAlerts['color_alerts']) && count($stockAlerts['color_alerts']) > 0)
            <div class="mt-4">
                <h4 class="text-sm font-medium text-purple-800 mb-2">
                    🎨 Alertes par Couleur ({{ count($stockAlerts['color_alerts']) }})
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($stockAlerts['color_alerts']->take(6) as $alert)
                        <div class="bg-purple-100 rounded px-2 py-1 text-xs text-purple-800">
                            <span class="font-medium">{{ $alert['product']->name }}</span>
                            <span class="text-purple-600">({{ $alert['color'] }}: {{ $alert['quantity'] }})</span>
                        </div>
                    @endforeach
                    @if(count($stockAlerts['color_alerts']) > 6)
                        <div class="bg-purple-100 rounded px-2 py-1 text-xs text-purple-800">
                            +{{ count($stockAlerts['color_alerts']) - 6 }} autre(s)...
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        <div class="mt-4 pt-3 border-t border-red-200">
            <div class="flex items-center justify-between">
                <p class="text-xs text-red-600">
                    <i class="fas fa-clock mr-1"></i>
                    Dernière vérification : {{ now()->format('d/m/Y H:i') }}
                </p>
                <a href="{{ route('admin.stock.index') }}" 
                   class="text-xs text-red-600 hover:text-red-800 underline">
                    Voir tous les détails →
                </a>
            </div>
        </div>
    </div>
@endif
