@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="glass-effect rounded-2xl shadow-2xl p-8">
    <!-- Logo/Header -->
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-user-plus text-2xl text-blue-600"></i>
        </div>
        <h2 class="text-3xl font-bold text-white">Create Account</h2>
        <p class="text-blue-200 mt-2">Join us today</p>
    </div>

    <!-- Registration Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-blue-200 mb-2">
                <i class="fas fa-user mr-2"></i>Full Name
            </label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                   placeholder="Enter your full name">
            @error('name')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-blue-200 mb-2">
                <i class="fas fa-envelope mr-2"></i>Email Address
            </label>
            <input type="email"
                   name="email"
                   id="email"
                   value="{{ old('email') }}"
                   required
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
                <i class="fas fa-lock mr-2"></i>Password
            </label>
            <div class="relative">
                <input type="password"
                       name="password"
                       id="password"
                       required
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                       placeholder="Create a password">
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
                <i class="fas fa-lock mr-2"></i>Confirm Password
            </label>
            <div class="relative">
                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       required
                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                       placeholder="Confirm your password">
                <button type="button"
                        onclick="togglePassword('password_confirmation', 'confirm-password-icon')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-blue-300 hover:text-white">
                    <i id="confirm-password-icon" class="fas fa-eye"></i>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-center">
            <input type="checkbox"
                   name="terms"
                   id="terms"
                   required
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="terms" class="ml-2 block text-sm text-blue-200">
                I agree to the
                <a href="#" class="text-white hover:text-blue-200 underline">Terms and Conditions</a>
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200 transform hover:scale-105">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-user-plus group-hover:text-blue-400"></i>
                </span>
                Create Account
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-blue-200">
                Already have an account?
                <a href="{{ route('login') }}"
                   class="font-medium text-white hover:text-blue-200 transition duration-200">
                    Sign in here
                </a>
            </p>
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
