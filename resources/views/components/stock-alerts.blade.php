@php
    // Récupérer les alertes de stock actives
    $stockAlerts = \App\Models\AdminMessage::where('is_active', true)
        ->where(function($query) {
            $query->where('title', 'like', '%RUPTURE%')
                  ->orWhere('title', 'like', '%STOCK FAIBLE%')
                  ->orWhere('title', 'like', '%STOCK RESTAURÉ%');
        })
        ->where('created_at', '>', now()->subDays(7))
        ->orderBy('priority', 'desc')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
@endphp

@if($stockAlerts->count() > 0)
<div class="stock-alerts-container mb-6">
    <div class="bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Alertes de Stock Actives
                    </h3>
                    <p class="text-sm text-red-700 mt-1">
                        {{ $stockAlerts->count() }} alerte(s) nécessitent votre attention
                    </p>
                </div>
            </div>
            <button onclick="toggleStockAlerts()" class="text-red-400 hover:text-red-600">
                <i class="fas fa-chevron-down" id="stockAlertsToggleIcon"></i>
            </button>
        </div>
    </div>

    <div class="stock-alerts-list hidden mt-3 space-y-2" id="stockAlertsList">
        @foreach($stockAlerts as $alert)
            <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm
                @if(str_contains($alert->title, 'RUPTURE')) border-l-4 border-l-red-500 bg-red-50
                @elseif(str_contains($alert->title, 'STOCK FAIBLE')) border-l-4 border-l-yellow-500 bg-yellow-50
                @else border-l-4 border-l-green-500 bg-green-50
                @endif">
                
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            @if(str_contains($alert->title, 'RUPTURE'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                    🚨 URGENT
                                </span>
                            @elseif(str_contains($alert->title, 'STOCK FAIBLE'))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">
                                    ⚠️ ALERTE
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                    ℹ️ INFO
                                </span>
                            @endif
                            
                            <span class="text-xs text-gray-500">
                                {{ $alert->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <h4 class="text-sm font-medium text-gray-900 mb-1">
                            {{ $alert->title }}
                        </h4>
                        
                        <p class="text-sm text-gray-700">
                            {{ $alert->message }}
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-2 ml-3">
                        @if(str_contains($alert->title, 'RUPTURE'))
                            <a href="{{ route('admin.color-stock.index') }}" 
                               class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-white hover:bg-red-50">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                        @elseif(str_contains($alert->title, 'STOCK FAIBLE'))
                            <a href="{{ route('admin.color-stock.index') }}" 
                               class="inline-flex items-center px-3 py-1 border border-yellow-300 rounded-md text-xs font-medium text-yellow-700 bg-white hover:bg-yellow-50">
                                <i class="fas fa-eye mr-1"></i>Voir
                            </a>
                        @endif
                        
                        <button onclick="markAlertAsRead({{ $alert->id }})" 
                                class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
        
        <div class="text-center pt-2">
            <a href="{{ route('admin.color-stock.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-list mr-2"></i>Voir toutes les alertes
            </a>
        </div>
    </div>
</div>

<script>
function toggleStockAlerts() {
    const list = document.getElementById('stockAlertsList');
    const icon = document.getElementById('stockAlertsToggleIcon');
    
    if (list.classList.contains('hidden')) {
        list.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        list.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

function markAlertAsRead(alertId) {
    // Marquer l'alerte comme lue (optionnel)
    fetch(`/admin/messages/${alertId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Masquer l'alerte de la liste
            const alertElement = event.target.closest('.stock-alerts-list > div');
            if (alertElement) {
                alertElement.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Erreur lors du marquage de l\'alerte:', error);
    });
}

// Mise à jour automatique des alertes toutes les 30 secondes
setInterval(() => {
    // Ici vous pourriez ajouter une mise à jour AJAX des alertes
    // fetch('/admin/stock-alerts/latest')
    //     .then(response => response.json())
    //     .then(data => updateAlerts(data));
}, 30000);

// Animation d'entrée pour les nouvelles alertes
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.stock-alerts-list > div');
    alerts.forEach((alert, index) => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(-20px)';
            alert.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                alert.style.opacity = '1';
                alert.style.transform = 'translateX(0)';
            }, 100);
        }, index * 100);
    });
});
</script>
@endif
