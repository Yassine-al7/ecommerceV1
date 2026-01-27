@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-6 md:mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">{{ __('admin_messages.title') }}</h1>
                        <p class="text-gray-600 mt-2">{{ __('admin_messages.subtitle') }}</p>
                    </div>
                    <a href="{{ route('admin.messages.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>{{ __('admin_messages.back') }}
                    </a>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form method="POST" action="{{ route('admin.messages.update', $message) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Titre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_messages.message_title') }}</label>
                        <input type="text" name="title" value="{{ old('title', $message->title) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="{{ __('admin_messages.message_title_ph') }}">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_messages.message_content') }}</label>
                        <textarea name="message" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="{{ __('admin_messages.message_content_ph') }}">{{ old('message', $message->message) }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type et Priorité -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type de Message -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_messages.type') }}</label>
                            <select name="type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('admin_messages.select_type') }}</option>
                                <option value="info" @selected(old('type', $message->type) === 'info')>{{ __('admin_messages.types.info') }}</option>
                                <option value="success" @selected(old('type', $message->type) === 'success')>{{ __('admin_messages.types.success') }}</option>
                                <option value="warning" @selected(old('type', $message->type) === 'warning')>{{ __('admin_messages.types.warning') }}</option>
                                <option value="error" @selected(old('type', $message->type) === 'error')>{{ __('admin_messages.types.error') }}</option>
                                <option value="celebration" @selected(old('type', $message->type) === 'celebration')>{{ __('admin_messages.types.celebration') }}</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priorité -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_messages.priority') }}</label>
                            <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('admin_messages.select_priority') }}</option>
                                <option value="low" @selected(old('priority', $message->priority) === 'low')>{{ __('admin_messages.priorities.low') }}</option>
                                <option value="medium" @selected(old('priority', $message->priority) === 'medium')>{{ __('admin_messages.priorities.medium') }}</option>
                                <option value="high" @selected(old('priority', $message->priority) === 'high')>{{ __('admin_messages.priorities.high') }}</option>
                                <option value="urgent" @selected(old('priority', $message->priority) === 'urgent')>{{ __('admin_messages.priorities.urgent') }}</option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Rôles Cibles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_messages.target_roles') }}</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="seller" @checked(in_array('seller', old('target_roles', $message->target_roles ?? [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">{{ __('admin_messages.roles.seller') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="admin" @checked(in_array('admin', old('target_roles', $message->target_roles ?? [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">{{ __('admin_messages.roles.admin') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="target_roles[]" value="user" @checked(in_array('user', old('target_roles', $message->target_roles ?? [])))
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">{{ __('admin_messages.roles.user') }}</span>
                            </label>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ __('admin_messages.target_hint') }}</p>
                    </div>

                    <!-- Date d'Expiration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin_messages.expires_at') }}</label>
                        <input type="datetime-local" name="expires_at"
                               value="{{ old('expires_at', $message->expires_at ? $message->expires_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">{{ __('admin_messages.expires_hint') }}</p>
                        @error('expires_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut Actif -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $message->is_active))
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ __('admin_messages.active_label') }}</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1">{{ __('admin_messages.active_hint') }}</p>
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="{{ route('admin.messages.index') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                            {{ __('admin_messages.cancel') }}
                        </a>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>{{ __('admin_messages.update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
