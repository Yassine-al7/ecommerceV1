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
                <h2 class="text-lg font-semibold text-gray-800">Messages Existants</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cibles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($messages as $message)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <i class="{{ $message->getIcon() }} text-lg {{ $message->type === 'celebration' ? 'text-purple-600' : 'text-gray-600' }}"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($message->type) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $message->title }}</div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($message->message, 80) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($message->priority === 'urgent') bg-red-100 text-red-800
                                        @elseif($message->priority === 'high') bg-orange-100 text-orange-800
                                        @elseif($message->priority === 'medium') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($message->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($message->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">Inactif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($message->expires_at)
                                        {{ $message->expires_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-gray-400">Jamais</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.messages.edit', $message) }}"
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.messages.toggle-status', $message) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                                <i class="fas fa-{{ $message->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
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
