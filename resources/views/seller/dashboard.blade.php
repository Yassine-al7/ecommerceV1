@extends('layouts.app')

@section('title', 'Seller Dashboard')

@section('content')
<div class="glass-effect rounded-2xl shadow-2xl p-8 w-full">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-tachometer-alt text-3xl text-blue-600"></i>
        </div>
        <h1 class="text-4xl font-bold text-white mb-2">Seller Dashboard</h1>
        <p class="text-blue-100 text-lg">Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <i class="fas fa-box text-4xl text-blue-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">24</h3>
            <p class="text-blue-100">Total Products</p>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <i class="fas fa-shopping-cart text-4xl text-green-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">156</h3>
            <p class="text-blue-100">Total Orders</p>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <i class="fas fa-dollar-sign text-4xl text-yellow-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">$12,450</h3>
            <p class="text-blue-100">Total Revenue</p>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <i class="fas fa-star text-4xl text-orange-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-white mb-2">4.8</h3>
            <p class="text-blue-100">Average Rating</p>
        </div>
    </div>

    <!-- Main Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('seller.products.index') }}"
           class="bg-white/10 rounded-xl p-8 text-center hover:bg-white/20 transition duration-200 card-hover group">
            <i class="fas fa-box text-5xl text-white mb-6 group-hover:text-blue-200 transition duration-200"></i>
            <h3 class="text-2xl font-bold text-white mb-3">Manage Products</h3>
            <p class="text-blue-100 mb-4">Add, edit, and organize your product inventory</p>
            <div class="bg-blue-500 text-white px-4 py-2 rounded-lg inline-block">
                <i class="fas fa-arrow-right mr-2"></i>Go to Products
            </div>
        </a>

        <div class="bg-white/10 rounded-xl p-8 text-center hover:bg-white/20 transition duration-200 card-hover group cursor-pointer">
            <i class="fas fa-shopping-cart text-5xl text-white mb-6 group-hover:text-green-200 transition duration-200"></i>
            <h3 class="text-2xl font-bold text-white mb-3">View Orders</h3>
            <p class="text-blue-100 mb-4">Track and manage your customer orders</p>
            <div class="bg-green-500 text-white px-4 py-2 rounded-lg inline-block">
                <i class="fas fa-arrow-right mr-2"></i>View Orders
            </div>
        </div>

        <div class="bg-white/10 rounded-xl p-8 text-center hover:bg-white/20 transition duration-200 card-hover group cursor-pointer">
            <i class="fas fa-chart-bar text-5xl text-white mb-6 group-hover:text-yellow-200 transition duration-200"></i>
            <h3 class="text-2xl font-bold text-white mb-3">Analytics</h3>
            <p class="text-blue-100 mb-4">View sales reports and performance metrics</p>
            <div class="bg-yellow-500 text-white px-4 py-2 rounded-lg inline-block">
                <i class="fas fa-arrow-right mr-2"></i>View Analytics
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white/5 rounded-xl p-6 mb-8">
        <h3 class="text-xl font-bold text-white mb-4">
            <i class="fas fa-clock mr-2"></i>Recent Activity
        </h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-plus-circle text-green-400 mr-3"></i>
                    <span class="text-blue-100">New product "Wireless Headphones" added</span>
                </div>
                <span class="text-blue-300 text-sm">2 hours ago</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-shopping-cart text-blue-400 mr-3"></i>
                    <span class="text-blue-100">Order #1234 received</span>
                </div>
                <span class="text-blue-300 text-sm">4 hours ago</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-edit text-yellow-400 mr-3"></i>
                    <span class="text-blue-100">Product "Gaming Mouse" updated</span>
                </div>
                <span class="text-blue-300 text-sm">1 day ago</span>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="text-center">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200 card-hover">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </button>
        </form>
    </div>
</div>
@endsection
