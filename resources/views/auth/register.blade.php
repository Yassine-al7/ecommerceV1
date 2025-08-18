@extends('layouts.auth')

@section('title', 'Inscription Vendeur')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Inscription Vendeur
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Créez votre compte vendeur pour commencer
            </p>
        </div>
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="role" value="seller">

            <div class="space-y-4">
                <!-- Nom complet -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom complet *</label>
                    <input id="name" name="name" type="text" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Votre nom complet" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Adresse email *</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="votre@email.com" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Numéro de téléphone -->
                <div>
                    <label for="numero_telephone" class="block text-sm font-medium text-gray-700">Numéro de téléphone *</label>
                    <input id="numero_telephone" name="numero_telephone" type="tel" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="+212 6 XX XX XX XX" value="{{ old('numero_telephone') }}">
                    @error('numero_telephone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom du magasin -->
                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700">Nom du magasin *</label>
                    <input id="store_name" name="store_name" type="text" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Nom de votre boutique" value="{{ old('store_name') }}">
                    @error('store_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RIB -->
                <div>
                    <label for="rib" class="block text-sm font-medium text-gray-700">RIB *</label>
                    <input id="rib" name="rib" type="text" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="007 810 0001000000000000" value="{{ old('rib') }}">
                    @error('rib')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe *</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Minimum 8 caractères">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmation du mot de passe -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe *</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Répétez votre mot de passe">
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Créer mon compte vendeur
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Déjà inscrit ? Se connecter
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
