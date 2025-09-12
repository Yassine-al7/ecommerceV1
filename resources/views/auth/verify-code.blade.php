@extends('layouts.auth')

@section('content')
<div class="max-w-md w-full space-y-8 card-gradient card-frame rounded-2xl shadow-2xl p-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-white/10 border border-white/20">
                <i class="fas fa-envelope text-white/80 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Vérification du Code
            </h2>
            <p class="mt-2 text-center text-sm text-blue-200">
                Nous avons envoyé un code de vérification à 6 chiffres à votre adresse email
            </p>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/30 text-green-200 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/30 text-red-200 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-500/20 border border-blue-500/30 text-blue-200 px-4 py-3 rounded">
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
                        <i class="fas fa-key text-white/60"></i>
                    </div>
                    <input id="verification_code" name="verification_code" type="text" required
                           class="appearance-none rounded-md relative block w-full px-3 py-3 pl-10 border border-white/20 placeholder-blue-300 text-white bg-white/10 focus:outline-none focus:ring-white/50 focus:border-transparent focus:z-10 sm:text-sm text-center tracking-widest"
                           placeholder="000000" maxlength="6" pattern="[0-9]{6}" autocomplete="off" autofocus>
                </div>
                @error('verification_code')
                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-[color:var(--sidebar-link)] hover:bg-[color:var(--sidebar-link-hover)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[color:var(--sidebar-link)]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-check text-white/70 group-hover:text-white"></i>
                    </span>
                    Vérifier le Code
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-blue-200">
                    Vous n'avez pas reçu le code ?
                    <a href="{{ route('register') }}" class="font-medium text-white hover:text-blue-200">
                        Recommencer l'inscription
                    </a>
                </p>

                @auth
                    @if(!Auth::user()->email_verified_at)
                        <div class="mt-4">
                            <form method="POST" action="{{ route('register.resend-code') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-white hover:text-blue-200 underline">
                                    Renvoyer un nouveau code
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-blue-200">
                Le code est valide pendant 15 minutes
            </p>
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


