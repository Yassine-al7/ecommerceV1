@extends('layouts.auth')

@section('title', 'Inscription Vendeur')

@section('content')
<div class="card-gradient card-frame rounded-2xl shadow-2xl p-8">
    <div class="text-center mb-6">
        <img src="{{ asset(config('branding.logo_path')) }}" alt="Logo" class="h-16 w-auto mx-auto rounded-md bg-white/10 p-2">
        <h2 class="mt-4 text-2xl font-bold text-white">{{ __('ui.register.title') }}</h2>
        <p class="mt-2 text-sm text-blue-200">{{ __('ui.register.subtitle') }}</p>
    </div>
    <form class="space-y-5" method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="role" value="seller">

            <div class="space-y-4">
                <!-- Nom complet -->
                <div class="form-field">
                    <input id="name" name="name" type="text" required class="input-dark" placeholder="{{ __('ui.fields.name') }}">
                    <label for="name" class="floating-label">{{ __('ui.fields.name') }} *</label>
                    @error('name')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-field">
                    <input id="email" name="email" type="email" autocomplete="email" required class="input-dark" placeholder="{{ __('ui.fields.email') }}">
                    <label for="email" class="floating-label">{{ __('ui.fields.email') }} *</label>
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Numéro de téléphone -->
                <div class="form-field">
                    <input id="numero_telephone" name="numero_telephone" type="tel" required class="input-dark" placeholder="{{ __('ui.fields.phone') }}">
                    <label for="numero_telephone" class="floating-label">{{ __('ui.fields.phone') }} *</label>
                    @error('numero_telephone')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom du magasin -->
                <div class="form-field">
                    <input id="store_name" name="store_name" type="text" required class="input-dark" placeholder="{{ __('ui.fields.store_name') }}">
                    <label for="store_name" class="floating-label">{{ __('ui.fields.store_name') }} *</label>
                    @error('store_name')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RIB -->
                <div class="form-field">
                    <input id="rib" name="rib" type="text" required class="input-dark" placeholder="{{ __('ui.fields.rib') }}">
                    <label for="rib" class="floating-label">{{ __('ui.fields.rib') }} *</label>
                    @error('rib')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="form-field">
                    <input id="password" name="password" type="password" autocomplete="new-password" required class="input-dark" placeholder="{{ __('ui.fields.password') }}">
                    <label for="password" class="floating-label">{{ __('ui.fields.password') }} *</label>
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmation du mot de passe -->
                <div class="form-field">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="input-dark" placeholder="{{ __('ui.fields.password_confirm') }}">
                    <label for="password_confirmation" class="floating-label">{{ __('ui.fields.password_confirm') }} *</label>
                    @error('password_confirmation')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-[color:var(--sidebar-link)] hover:bg-[color:var(--sidebar-link-hover)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[color:var(--sidebar-link)]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    {{ __('ui.register.create') }}
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium link-brand">{{ __('ui.register.already') }} {{ __('ui.register.login') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
