@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="card-gradient card-frame rounded-2xl shadow-2xl p-8">
    <!-- Logo/Header -->
    <div class="text-center mb-8">
        <div class="mx-auto mb-4">
            <div class="affilook-logo text-4xl mx-auto">Affilook</div>
        </div>
        <h2 class="text-3xl font-bold text-white">Reset Password</h2>
        <p class="text-blue-200 mt-2">Enter your email to receive a reset link</p>
    </div>

    @if (session('status'))
        <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg">
            <p class="text-green-200 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
            </p>
        </div>
    @endif

    <!-- Reset Form -->
    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

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
                   autofocus
                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                   placeholder="Enter your email address">
            @error('email')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-[color:var(--sidebar-link)] hover:bg-[color:var(--sidebar-link-hover)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[color:var(--sidebar-link)] transition duration-200 transform hover:scale-105">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-paper-plane"></i>
                </span>
                Send Reset Link
            </button>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <p class="text-sm text-blue-200">
                Remember your password?
                <a href="{{ route('login') }}"
                   class="font-medium text-white hover:text-blue-200 transition duration-200">
                    Back to login
                </a>
            </p>
        </div>
    </form>
</div>
@endsection
