@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-6 md:mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Gestion des Messages</h1>
                    <p class="text-gray-600 mt-2">Envoyez des messages et alertes à tous les vendeurs</p>
                </div>
                <a href="{{ route('admin.messages.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Nouveau Message</span>
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-600">Total Messages</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                <div class="text-sm text-gray-600">Messages Actifs</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $stats['urgent'] }}</div>
                <div class="text-sm text-gray-600">Messages Urgents</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['celebration'] }}</div>
                <div class="text-sm text-gray-600">Félicitations</div>
            </div>
        </div>

        <!-- Liste des Messages -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Messages Existants</h2>

                    <!-- Actions en lot -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-3" id="bulkActions" style="display: none;">
                        <span class="text-sm text-gray-600" id="selectedCount">0 sélectionné(s)</span>
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <button onclick="bulkToggleStatus()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg text-xs sm:text-sm transition-colors whitespace-nowrap">
                                <i class="fas fa-toggle-on mr-1 sm:mr-2"></i><span class="hidden sm:inline">Activer/Désactiver</span><span class="sm:hidden">Toggle</span>
                            </button>
                            <button onclick="bulkDelete()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs sm:text-sm transition-colors whitespace-nowrap">
                                <i class="fas fa-trash mr-1 sm:mr-2"></i><span class="hidden sm:inline">Supprimer</span><span class="sm:hidden">Del</span>
                            </button>
                            <button onclick="clearSelection()" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-xs sm:text-sm transition-colors whitespace-nowrap">
                                <i class="fas fa-times mr-1 sm:mr-2"></i><span class="hidden sm:inline">Annuler</span><span class="sm:hidden">Annul</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleSelectAll()">
                            </th>
                            <th class="hidden sm:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                            <th class="hidden md:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                            <th class="hidden lg:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cibles</th>
                            <th class="hidden sm:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="hidden lg:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expire</th>
                            <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                                                @forelse($messages as $message)
                            <tr class="hover:bg-gray-50" data-message-id="{{ $message->id }}">
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_messages[]" value="{{ $message->id }}"
                                           class="message-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           onchange="updateSelection()">
                                </td>
                                <td class="hidden sm:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <i class="{{ $message->getIcon() }} text-lg {{ $message->type === 'celebration' ? 'text-purple-600' : 'text-gray-600' }}"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($message->type) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $message->title }}</div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($message->message, 80) }}</div>
                                </td>
                                <td class="hidden md:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($message->priority === 'urgent') bg-red-100 text-red-800
                                        @elseif($message->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($message->priority === 'medium') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($message->priority) }}
                                    </span>
                                </td>
                                <td class="hidden lg:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                                    @if($message->target_roles)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($message->target_roles as $role)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst($role) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">Tous les rôles</span>
                                    @endif
                                </td>
                                <td class="hidden sm:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                                    @if($message->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">Inactif</span>
                                    @endif
                                </td>
                                <td class="hidden lg:table-cell px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($message->expires_at)
                                        {{ $message->expires_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-gray-400">Jamais</span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2 md:space-x-3">
                                        <a href="{{ route('admin.messages.edit', $message) }}"
                                           class="inline-flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"
                                           title="Modifier">
                                            <i class="fas fa-edit text-xs md:text-sm"></i>
                                        </a>

                                        <button type="button"
                                                onclick="toggleMessageStatus({{ $message->id }}, {{ $message->is_active ? 'true' : 'false' }})"
                                                class="inline-flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-full {{ $message->is_active ? 'bg-green-100 text-green-600 hover:bg-green-200' : 'bg-yellow-100 text-yellow-600 hover:bg-yellow-200' }} transition-colors"
                                                title="{{ $message->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-{{ $message->is_active ? 'pause' : 'play' }} text-xs md:text-sm"></i>
                                        </button>

                                        <button type="button"
                                                onclick="deleteMessage({{ $message->id }})"
                                                class="inline-flex items-center justify-center w-7 h-7 md:w-8 md:h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                                title="Supprimer">
                                            <i class="fas fa-trash text-xs md:text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Aucun message trouvé. <a href="{{ route('admin.messages.create') }}" class="text-blue-600 hover:underline">Créer le premier message</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($messages->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-messages.js') }}"></script>
@endpush
