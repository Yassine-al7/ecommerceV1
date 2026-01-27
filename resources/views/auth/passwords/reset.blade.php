@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="glass-effect rounded-2xl shadow-2xl p-8">
    <!-- Logo/Header -->
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-lock-open text-2xl text-blue-600"></i>
        </div>
        <h2 class="text-3xl font-bold text-white">Set New Password</h2>
        <p class="text-blue-200 mt-2">Enter your new password below</p>
    </div>

    <!-- Reset Form -->
    <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-blue-200 mb-2">
                <i class="fas fa-envelope mr-2"></i>Email Address
            </label>
            <input type="email"
                   name="email"
                   id="email"
                   value="{{ $email ?? old('email') }}"
                   required
                   autofocus
                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                   placeholder="Enter your email">
            @error('email')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-blue-200 mb-2">
                <i class="fas fa-lock mr-2"></i>New Password
            </label>
            <div class="relative">
                <input type="password"
                       name="password"
                       id="password"
                       required
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                       placeholder="Enter new password">
                <button type="button"
                        onclick="togglePassword('password', 'password-icon')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-white">
                    <i id="password-icon" class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-blue-200 mb-2">
                <i class="fas fa-lock mr-2"></i>Confirm New Password
            </label>
            <div class="relative">
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       required
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                       placeholder="Confirm new password">
                <button type="button"
                        onclick="togglePassword('password_confirmation', 'confirm-password-icon')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-white">
                    <i id="confirm-password-icon" class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-105">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-check group-hover:text-blue-400"></i>
                </span>
                Reset Password
            </button>
        </div>
    </form>
</div>

<script>
function togglePassword(fieldId, iconId) {
    const passwordField = document.getElementById(fieldId);
    const passwordIcon = document.getElementById(iconId);

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
