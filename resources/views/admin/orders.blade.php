@extends('layouts.app')

@section('title', 'Gestion des Commandes')

@php
use App\Helpers\OrderHelper;
@endphp

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Cartes de statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Commande en attente -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En Attente</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['en attente'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande confirmée -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Confirmé</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['confirme'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande livrée -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-truck text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Livré</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['livre'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande pas de réponse -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-orange-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-phone text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pas de Réponse</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pas de réponse'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande problématique -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Problématique</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['problematique'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Filtre par statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="en attente">En attente</option>
                        <option value="confirme">Confirmé</option>
                        <option value="en livraison">En livraison</option>
                        <option value="livre">Livré</option>
                        <option value="pas de réponse">Pas de réponse</option>
                        <option value="problematique">Problématique</option>
                    </select>
                </div>

                <!-- Filtre par date de début -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                    <input type="date" id="dateDebut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filtre par date de fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                    <input type="date" id="dateFin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Boutons de filtrage -->
            <div class="flex items-center justify-between mt-4">
                <div class="flex space-x-2">
                    <button onclick="applyFilters()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-filter mr-2"></i>Appliquer les filtres
                    </button>
                    <button onclick="resetFilters()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-undo mr-2"></i>Réinitialiser
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    <span id="totalOrders">{{ $orders->total() }}</span> commande(s) trouvée(s)
                </div>
            </div>
        </div>

        <!-- Tableau des commandes -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestion des Commandes</h1>
                <a href="{{ route('admin.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nouvelle Commande
                </a>
            </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Référence
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Client
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Vendeur
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Ville
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Commentaire
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Prix Total
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white" id="ordersTableBody">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 {{ $order->commentaire ? 'bg-yellow-50 border-l-4 border-yellow-400' : '' }}" data-status="{{ $order->status }}" data-date="{{ $order->created_at->format('Y-m-d') }}">
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 text-gray-900">{{ $order->reference }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 text-gray-900">{{ $order->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs leading-4 text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 text-gray-900">{{ $order->nom_client }}</div>
                                    <div class="text-sm leading-5 text-gray-500">{{ $order->numero_telephone_client }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 text-gray-900">{{ $order->seller->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 text-gray-900">{{ $order->ville }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <!-- Statut avec nouvelle logique -->
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ OrderHelper::getStatusColor($order->status) }}">
                                            {{ OrderHelper::getStatusLabel($order->status) }}
                                        </span>

                                        <!-- Bouton de modification -->
                                        <button onclick="toggleStatusEdit({{ $order->id }})"
                                                class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>

                                    <!-- Formulaire de modification du statut -->
                                    <form id="statusForm{{ $order->id }}" method="POST"
                                          action="{{ route('admin.orders.update-status', $order->id) }}"
                                          class="hidden mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                                class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 w-full">
                                            <option value="en attente" @selected($order->status == 'en attente')>En attente</option>
                                            <option value="non confirmé" @selected($order->status == 'non confirmé')>Non confirmé</option>
                                            <option value="confirme" @selected($order->status == 'confirme')>Confirmé</option>
                                            <option value="en livraison" @selected($order->status == 'en livraison')>En livraison</option>
                                            <option value="livre" @selected($order->status == 'livre')>Livré</option>
                                            <option value="pas de réponse" @selected($order->status == 'pas de réponse')>Pas de réponse</option>
                                            <option value="annulé" @selected($order->status == 'annulé')>Annulé</option>
                                            <option value="retourné" @selected($order->status == 'retourné')>Retourné</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    @if($order->commentaire)
                                        <div class="max-w-xs">
                                            <div class="text-sm text-gray-900 font-medium mb-1">Commentaire :</div>
                                            <div class="text-sm text-gray-700 bg-yellow-50 p-2 rounded border-l-4 border-yellow-400">
                                                {{ $order->commentaire }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 italic">Aucun commentaire</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 text-gray-900 font-medium">{{ number_format($order->prix_commande, 2) }} MAD</div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                    Aucune commande trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleStatusEdit(orderId) {
    const form = document.getElementById('statusForm' + orderId);
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
    } else {
        form.classList.add('hidden');
    }
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const dateDebut = document.getElementById('dateDebut').value;
    const dateFin = document.getElementById('dateFin').value;

    const rows = document.querySelectorAll('#ordersTableBody tr[data-status]');
    let visibleCount = 0;

    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const date = row.getAttribute('data-date');

        let showRow = true;

        // Filtre par statut
        if (statusFilter && statusFilter !== '') {
            if (statusFilter === 'problematique') {
                showRow = ['annulé', 'retourné'].includes(status); // Plus 'pas de réponse'
            } else if (statusFilter === 'confirme') {
                showRow = ['confirme', 'en livraison'].includes(status); // Inclut 'en livraison'
            } else if (statusFilter === 'en attente') {
                showRow = ['en attente', 'non confirmé'].includes(status); // Inclut 'non confirmé'
            } else {
                showRow = status === statusFilter;
            }
        }

        // Filtre par date
        if (dateDebut && date < dateDebut) {
            showRow = false;
        }
        if (dateFin && date > dateFin) {
            showRow = false;
        }

        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('totalOrders').textContent = visibleCount;
}

function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateDebut').value = '';
    document.getElementById('dateFin').value = '';

    const rows = document.querySelectorAll('#ordersTableBody tr[data-status]');
    rows.forEach(row => {
        row.style.display = '';
    });

    document.getElementById('totalOrders').textContent = rows.length;
}

// Initialiser les filtres au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Définir la date d'aujourd'hui comme date de fin par défaut
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateFin').value = today;

    // Définir la date d'il y a 30 jours comme date de début par défaut
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    document.getElementById('dateDebut').value = thirtyDaysAgo.toISOString().split('T')[0];
});
</script>
@endsection


