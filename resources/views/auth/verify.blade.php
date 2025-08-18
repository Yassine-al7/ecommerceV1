@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="max-w-xl mx-auto glass-effect rounded-2xl shadow-2xl p-8">
    <h1 class="text-3xl font-bold text-white mb-4">Vérifiez votre adresse e-mail</h1>

    @if (session('status'))
        <div class="mb-4 p-3 rounded bg-green-500/20 border border-green-500/40 text-green-100">
            {{ session('status') }}
        </div>
    @endif

    <p class="text-blue-100 mb-6">
        Un lien de vérification vous a été envoyé par e-mail. Si vous n'avez pas reçu l'e-mail, vous pouvez demander un nouveau lien ci-dessous.
    </p>

    <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
        @csrf
        <button type="submit" class="bg-white text-blue-600 font-bold py-3 px-6 rounded-lg hover:bg-blue-50 transition duration-200">
            Renvoyer le lien de vérification
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button class="text-blue-100 hover:underline">Se déconnecter</button>
    </form>
</div>
@endsection


