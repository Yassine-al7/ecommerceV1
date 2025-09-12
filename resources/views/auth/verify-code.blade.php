@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <i class="fas fa-envelope text-blue-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Vérification du Code
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Nous avons envoyé un code de vérification à 6 chiffres à votre adresse email
            </p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('register.verify.code') }}" id="verifyForm">
            @csrf

            <div>
                <label for="verification_code" class="sr-only">Code de vérification</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-key text-gray-400"></i>
                    </div>
                    <input id="verification_code" name="verification_code" type="text" required
                           class="appearance-none rounded-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm text-center tracking-widest"
                           placeholder="000000" maxlength="6" pattern="[0-9]{6}" autocomplete="off" autofocus>
                </div>
                @error('verification_code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-check text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Vérifier le Code
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Vous n'avez pas reçu le code ?
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Recommencer l'inscription
                    </a>
                </p>

                @auth
                    @if(!Auth::user()->email_verified_at)
                        <div class="mt-4">
                            <form method="POST" action="{{ route('register.resend-code') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-blue-600 hover:text-blue-500 underline">
                                    Renvoyer un nouveau code
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                Le code est valide pendant 15 minutes
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('verification_code');
    const form = document.getElementById('verifyForm');

    // Auto-focus sur le champ
    input.focus();

    // Formater automatiquement le code
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 6) {
            value = value.slice(0, 6);
        }
        e.target.value = value;
    });

    // Soumettre automatiquement quand 6 chiffres sont saisis
    input.addEventListener('keyup', function(e) {
        if (e.target.value.length === 6) {
            // Rafraîchir le token CSRF avant soumission
            fetch('/csrf-token')
                .then(response => response.json())
                .then(data => {
                    const csrfInput = form.querySelector('input[name="_token"]');
                    if (csrfInput) {
                        csrfInput.value = data.token;
                    }
                    form.submit();
                })
                .catch(() => {
                    // Si la route n'existe pas, soumettre directement
                    form.submit();
                });
        }
    });

    // Rafraîchir le token CSRF toutes les 5 minutes
    setInterval(() => {
        fetch('/csrf-token')
            .then(response => response.json())
            .then(data => {
                const csrfInput = form.querySelector('input[name="_token"]');
                if (csrfInput) {
                    csrfInput.value = data.token;
                }
            })
            .catch(() => {
                // Ignorer les erreurs
            });
    }, 300000); // 5 minutes
});
</script>
@endsection


