@extends('layouts.app')

@section('title', __('admin_orders.title'))

@php
use App\Helpers\OrderHelper;
@endphp

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Cartes de statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Commande en attente -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.pending', [], 'ar') }}</p>
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
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.confirmed', [], 'ar') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['confirmé'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande expédition -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-shipping-fast text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.shipping', [], 'ar') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['expédition'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande livrée -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.delivered', [], 'ar') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['livré'] ?? 0 }}</p>
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
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.no_answer', [], 'ar') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pas de réponse'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande annulé -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.cancelled', [], 'ar') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['annulé'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande reporté -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-orange-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.postponed', [], 'ar') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['reporté'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande retourné -->
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-gray-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                        <i class="fas fa-undo text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.returned', [], 'ar') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['retourné'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Commande problématique (total) -->
            <div class="col-span-2 lg:col-span-1 bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-400">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin_orders.stats.issue', [], 'ar') }}</p>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_orders.filters.status') }}</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">{{ __('admin_orders.filters.all_statuses') }}</option>
                        <option value="en attente">{{ OrderHelper::getStatusLabel('en attente') }}</option>
                        <option value="confirmé">{{ OrderHelper::getStatusLabel('confirmé') }}</option>
                        <option value="expédition">{{ OrderHelper::getStatusLabel('expédition') }}</option>
                        <option value="livré">{{ OrderHelper::getStatusLabel('livré') }}</option>
                        <option value="pas de réponse">{{ OrderHelper::getStatusLabel('pas de réponse') }}</option>
                        <option value="reporté">{{ OrderHelper::getStatusLabel('reporté') }}</option>
                        <option value="retourné">{{ OrderHelper::getStatusLabel('retourné') }}</option>
                        <option value="annulé">{{ OrderHelper::getStatusLabel('annulé') }}</option>
                        <option value="problematique">{{ OrderHelper::getStatusLabel('problematique') }}</option>
                    </select>
                </div>

                <!-- Filtre par date de début -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_orders.filters.start_date') }}</label>
                    <input type="date" id="dateDebut" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filtre par date de fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_orders.filters.end_date') }}</label>
                    <input type="date" id="dateFin" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Boutons de filtrage -->
            <div class="flex items-center justify-between mt-4">
                <div class="flex space-x-2">
                    <button onclick="applyFilters()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-filter mr-2"></i>{{ __('admin_orders.filters.apply') }}
                    </button>
                    <button onclick="resetFilters()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-undo mr-2"></i>{{ __('admin_orders.filters.reset') }}
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    <span id="totalOrders">{{ $orders->total() }}</span> {{ __('admin_orders.filters.found', ['count' => '']) }}
                </div>
            </div>
        </div>

        <!-- Tableau des commandes -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">{{ __('admin_orders.title') }}</h1>
                <a href="{{ route('admin.orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>{{ __('admin_orders.actions.new') }}
                </a>
            </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

            <!-- Barre d'actions en lot -->
            <div id="bulkActionsBar" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-blue-800">
                            <span id="selectedCount">0</span> {{ __('admin_orders.bulk.selected', ['count' => '']) }}
                        </span>
                        <div class="flex items-center space-x-2">
                            <button id="bulkStatusBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                <i class="fas fa-edit mr-1"></i>{{ __('admin_orders.bulk.edit_status') }}
                            </button>
                            <button id="bulkExportBtn" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                <i class="fas fa-download mr-1"></i>{{ __('admin_orders.bulk.export') }}
                            </button>
                            <button id="bulkDeleteBtn" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                <i class="fas fa-trash mr-1"></i>{{ __('admin_orders.bulk.delete') }}
                            </button>
                        </div>
                    </div>
                    <button id="clearSelectionBtn" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-times mr-1"></i>{{ __('admin_orders.bulk.clear') }}
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" title="{{ __('admin_orders.table.select_all_title') }}">
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_orders.table.reference') }}
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_orders.table.date') }}
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_orders.table.client') }}
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.fields.store_name') }}
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_orders.table.city') }}
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_orders.table.status') }}
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_orders.table.comment') }}
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_orders.table.total') }}
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin_orders.table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white" id="ordersTableBody">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 {{ $order->commentaire ? 'bg-yellow-50 border-l-4 border-yellow-400' : '' }}" data-status="{{ $order->status }}" data-date="{{ $order->created_at->format('Y-m-d') }}" data-order-id="{{ $order->id }}">
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <input type="checkbox" class="order-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $order->id }}" title="{{ __('admin_orders.table.select_row_title') }}">
                                </td>
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
                                    <div class="text-sm leading-5 text-gray-900">{{ $order->seller->store_name ?? ($order->seller->name ?? 'store') }}</div>
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
                                            <option value="en attente" @selected($order->status == 'en attente')>{{ OrderHelper::getStatusLabel('en attente') }}</option>
                                            <option value="confirmé" @selected($order->status == 'confirmé')>{{ OrderHelper::getStatusLabel('confirmé') }}</option>
                                            <option value="pas de réponse" @selected($order->status == 'pas de réponse')>{{ OrderHelper::getStatusLabel('pas de réponse') }}</option>
                                            <option value="expédition" @selected($order->status == 'expédition')>{{ OrderHelper::getStatusLabel('expédition') }}</option>
                                            <option value="livré" @selected($order->status == 'livré')>{{ OrderHelper::getStatusLabel('livré') }}</option>
                                            <option value="annulé" @selected($order->status == 'annulé')>{{ OrderHelper::getStatusLabel('annulé') }}</option>
                                            <option value="reporté" @selected($order->status == 'reporté')>{{ OrderHelper::getStatusLabel('reporté') }}</option>
                                            <option value="retourné" @selected($order->status == 'retourné')>{{ OrderHelper::getStatusLabel('retourné') }}</option>
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
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500">
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
                showRow = ['annulé', 'retourné', 'reporté', 'pas de réponse'].includes(status);
            } else if (statusFilter === 'confirmé') {
                showRow = ['confirmé', 'expédition'].includes(status);
            } else if (statusFilter === 'en attente') {
                showRow = ['en attente'].includes(status);
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

    // Initialiser la gestion de la sélection des lignes
    initializeRowSelection();
});

// Gestion de la sélection des lignes
function initializeRowSelection() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');

    // Gestion de "Sélectionner tout"
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        orderCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        updateBulkActionsBar();
    });

    // Gestion des checkboxes individuelles
    orderCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionsBar();
            updateSelectAllState();
        });
    });

    // Effacer la sélection
    clearSelectionBtn.addEventListener('click', function() {
        selectAllCheckbox.checked = false;
        orderCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateBulkActionsBar();
    });

    // Actions en lot
    document.getElementById('bulkStatusBtn').addEventListener('click', function() {
        const selectedOrders = getSelectedOrders();
        if (selectedOrders.length > 0) {
            bulkUpdateStatus(selectedOrders);
        }
    });

    document.getElementById('bulkExportBtn').addEventListener('click', function() {
        const selectedOrders = getSelectedOrders();
        if (selectedOrders.length > 0) {
            bulkExport(selectedOrders);
        }
    });

    document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
        const selectedOrders = getSelectedOrders();
        if (selectedOrders.length > 0) {
            bulkDelete(selectedOrders);
        }
    });
}

// Mettre à jour la barre d'actions en lot
function updateBulkActionsBar() {
    const selectedOrders = getSelectedOrders();
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedOrders.length > 0) {
        bulkActionsBar.classList.remove('hidden');
        selectedCount.textContent = selectedOrders.length;
    } else {
        bulkActionsBar.classList.add('hidden');
        selectedCount.textContent = '0';
    }
}

// Mettre à jour l'état de "Sélectionner tout"
function updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const checkedCheckboxes = document.querySelectorAll('.order-checkbox:checked');

    if (checkedCheckboxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkedCheckboxes.length === orderCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
        selectAllCheckbox.checked = false;
    }
}

// Obtenir les commandes sélectionnées
function getSelectedOrders() {
    const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
    return Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
}

// Afficher une notification
function showNotification(message, type = 'info') {
    // Supprimer les anciennes notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Créer la nouvelle notification
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;

    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Actions en lot
function bulkUpdateStatus(orderIds) {
    const newStatus = prompt('Entrez le nouveau statut (en attente, confirme, livre, etc.) :');
    if (newStatus && newStatus.trim()) {
        if (confirm(`Voulez-vous vraiment modifier le statut de ${orderIds.length} commande(s) vers "${newStatus}" ?`)) {
            // Ici vous pouvez implémenter la logique de mise à jour en lot
            console.log('Mise à jour en lot du statut vers:', newStatus, 'pour les commandes:', orderIds);
            alert('Fonctionnalité de mise à jour en lot à implémenter côté serveur');
        }
    }
}

function bulkExport(orderIds) {
    console.log('Export en lot des commandes:', orderIds);
    alert('Fonctionnalité d\'export en lot à implémenter côté serveur');
}

function bulkDelete(orderIds) {
    if (confirm(`Voulez-vous vraiment supprimer ${orderIds.length} commande(s) ? Cette action est irréversible.`)) {
        // Afficher un indicateur de chargement
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const originalText = bulkDeleteBtn.innerHTML;
        bulkDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Suppression...';
        bulkDeleteBtn.disabled = true;

        // Debug: afficher les données envoyées
        console.log('🔍 Données envoyées:', {
            url: '{{ route("admin.orders.bulk-delete") }}',
            order_ids: orderIds,
            csrf_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        });

        // Envoyer la requête de suppression en lot
        fetch('{{ route("admin.orders.bulk-delete") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                order_ids: orderIds
            })
        })
                .then(response => {
            console.log('🔍 Réponse reçue:', {
                status: response.status,
                statusText: response.statusText,
                headers: Object.fromEntries(response.headers.entries())
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return response.json();
        })
        .then(data => {
            console.log('🔍 Données reçues:', data);

            if (data.success) {
                // Supprimer les lignes du tableau
                orderIds.forEach(orderId => {
                    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                    if (row) {
                        row.remove();
                    }
                });

                // Afficher le message de succès
                showNotification(data.message, 'success');

                // Mettre à jour le compteur total
                const totalOrdersElement = document.getElementById('totalOrders');
                if (totalOrdersElement) {
                    const currentTotal = parseInt(totalOrdersElement.textContent);
                    totalOrdersElement.textContent = Math.max(0, currentTotal - data.deleted_count);
                }

                // Effacer la sélection
                document.getElementById('selectAll').checked = false;
                document.querySelectorAll('.order-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateBulkActionsBar();

            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Erreur lors de la suppression en lot:', error);
            showNotification(`Erreur lors de la suppression en lot: ${error.message}`, 'error');
        })
        .finally(() => {
            // Restaurer le bouton
            bulkDeleteBtn.innerHTML = originalText;
            bulkDeleteBtn.disabled = false;
        });
    }
}
</script>
@endsection


