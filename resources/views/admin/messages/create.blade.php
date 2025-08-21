@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6 md:mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Créer un Nouveau Message</h1>
                        <p class="text-gray-600 mt-2">Envoyez un message d'alerte ou de félicitation à tous les vendeurs</p>
                    </div>
                    <a href="{{ route('admin.messages.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form method="POST" action="{{ route('admin.messages.store') }}" class="space-y-6">
                    @csrf

                    <!-- Titre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titre du Message *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: Félicitations pour les ventes du mois">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contenu du Message *</label>
                        <textarea name="message" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Ex: Félicitations à tous nos vendeurs pour leurs excellentes performances ce mois-ci ! Vous avez dépassé nos objectifs de vente.">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type et Priorité -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type de Message -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de Message *</label>
                            <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner un type</option>
                                <option value="info" @selected(old('type') === 'info')>ℹ️ Information</option>
                                <option value="success" @selected(old('type') === 'success')>✅ Succès</option>
                                <option value="warning" @selected(old('type') === 'warning')>⚠️ Avertissement</option>
                                <option value="error" @selected(old('type') === 'error')>❌ Erreur</option>
                                <option value="celebration" @selected(old('type') === 'celebration')>🏆 Félicitations</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priorité -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priorité *</label>
                            <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner une priorité</option>
                                <option value="low" @selected(old('priority') === 'low')>🟢 Faible</option>
                                <option value="medium" @selected(old('priority') === 'medium')>🟡 Moyenne</option>
                                <option value="high" @selected(old('priority') === 'high')>🟠 Élevée</option>
                                <option value="urgent" @selected(old('priority') === 'urgent')>🔴 Urgente</option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Rôles Cibles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rôles Cibles</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="seller" @checked(in_array('seller', old('target_roles', [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Vendeurs</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="admin" @checked(in_array('admin', old('target_roles', [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Administrateurs</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="user" @checked(in_array('user', old('target_roles', [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Utilisateurs</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Laissez vide pour cibler tous les rôles</p>
                        @error('target_roles')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date d'Expiration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date d'Expiration</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Laissez vide si le message ne doit jamais expirer</p>
                        @error('expires_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>Envoyer le Message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Aperçu en Temps Réel -->
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aperçu du Message</h3>
                <div id="messagePreview" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <p class="text-gray-500 text-center">Remplissez le formulaire pour voir l'aperçu</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Aperçu en temps réel du message
function updatePreview() {
    const title = document.querySelector('input[name="title"]').value;
    const message = document.querySelector('textarea[name="message"]').value;
    const type = document.querySelector('select[name="type"]').value;
    const priority = document.querySelector('select[name="priority"]').value;

    const preview = document.getElementById('messagePreview');

    if (!title && !message) {
        preview.innerHTML = '<p class="text-gray-500 text-center">Remplissez le formulaire pour voir l\'aperçu</p>';
        return;
    }

    const typeClasses = {
        'info': 'bg-blue-50 border-blue-200 text-blue-800',
        'success': 'bg-green-50 border-green-200 text-green-800',
        'warning': 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'error': 'bg-red-50 border-red-200 text-red-800',
        'celebration': 'bg-purple-50 border-purple-200 text-purple-800'
    };

    const priorityClasses = {
        'low': 'border-l-4 border-l-gray-400',
        'medium': 'border-l-4 border-l-blue-400',
        'high': 'border-l-4 border-l-orange-400',
        'urgent': 'border-l-4 border-l-red-400'
    };

    const typeIcons = {
        'info': 'fas fa-info-circle',
        'success': 'fas fa-check-circle',
        'warning': 'fas fa-exclamation-triangle',
        'error': 'fas fa-times-circle',
        'celebration': 'fas fa-trophy'
    };

    const typeLabels = {
        'info': 'Information',
        'success': 'Succès',
        'warning': 'Avertissement',
        'error': 'Erreur',
        'celebration': 'Félicitations'
    };

    const priorityLabels = {
        'low': 'Faible',
        'medium': 'Moyenne',
        'high': 'Élevée',
        'urgent': 'Urgente'
    };

    const typeClass = typeClasses[type] || 'bg-gray-50 border-gray-200 text-gray-800';
    const priorityClass = priorityClasses[priority] || 'border-l-4 border-l-gray-400';
    const icon = typeIcons[type] || 'fas fa-bell';
    const typeLabel = typeLabels[type] || 'Message';
    const priorityLabel = priorityLabels[priority] || 'Normale';

    preview.innerHTML = `
        <div class="border rounded-lg p-4 ${typeClass} ${priorityClass}">
            <div class="flex items-center space-x-2 mb-2">
                <i class="${icon} text-lg"></i>
                <span class="font-semibold">${typeLabel}</span>
                <span class="text-xs px-2 py-1 bg-white/50 rounded-full">${priorityLabel}</span>
            </div>
            ${title ? `<h4 class="font-bold mb-2">${title}</h4>` : ''}
            ${message ? `<p class="text-sm">${message}</p>` : ''}
        </div>
    `;
}

// Écouter les changements dans le formulaire
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('input', updatePreview);
    element.addEventListener('change', updatePreview);
});

// Initialiser l'aperçu
document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endsection
