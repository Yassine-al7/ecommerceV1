@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="card-gradient card-frame rounded-2xl shadow-2xl p-8">
    <!-- Logo/Header -->
    <div class="text-center mb-8">
        <div class="mx-auto mb-4">
            <img src="{{ asset(config('branding.logo_path')) }}" alt="Logo" class="h-20 w-auto mx-auto rounded-md bg-white/10 p-2">
        </div>
        <h2 class="text-3xl font-bold text-white">{{ __('ui.login.title') }}</h2>
        <p class="text-blue-200 mt-2">{{ __('ui.login.subtitle') }}</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="font-medium">Erreurs de validation :</span>
            </div>
            <ul class="list-disc list-inside ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Field (floating) -->
        <div class="form-field">
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                   class="input-dark" placeholder="Email">
            <label for="email" class="floating-label">
                <i class="fas fa-envelope mr-2"></i>{{ __('ui.fields.email') }}
            </label>
            @error('email')
                <p class="error-text"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field (floating) -->
        <div class="form-field">
            <input type="password" name="password" id="password" required class="input-dark" placeholder="Password">
            <label for="password" class="floating-label">
                <i class="fas fa-lock mr-2"></i>{{ __('ui.fields.password') }}
            </label>
            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-white">
                <i id="password-icon" class="fas fa-eye"></i>
            </button>
            @error('password')
                <p class="error-text"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input type="checkbox"
                       name="remember"
                       id="remember"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-blue-200">
                    Remember me
                </label>
            </div>
            <div class="text-sm">
                <a href="{{ route('password.request') }}"
                   class="font-medium link-brand transition duration-200">
                    {{ __('ui.login.forgot_password') }}
                </a>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-[color:var(--sidebar-link)] hover:bg-[color:var(--sidebar-link-hover)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[color:var(--sidebar-link)] transition duration-200 transform hover:scale-105">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-sign-in-alt"></i>
                </span>
                {{ __('ui.login.sign_in') }}
            </button>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-sm text-blue-200">
                {{ __('ui.login.no_account') }}
                <a href="{{ route('register') }}"
                   class="font-medium link-brand transition duration-200">
                    {{ __('ui.login.create_now') }}
                </a>
            </p>
        </div>
    </form>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
