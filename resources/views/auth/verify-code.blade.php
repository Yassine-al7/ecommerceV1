@extends('layouts.auth')

@section('title', 'Enter Verification Code')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold mb-4">Vérification par code</h1>

        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.verify.submit') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email) }}">

            <div>
                <label class="block text-sm font-medium text-gray-700">Code reçu par email</label>
                <input type="text" name="code" value="{{ old('code') }}" maxlength="10"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('code')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Valider</button>
        </form>
    </div>
    </div>
@endsection


