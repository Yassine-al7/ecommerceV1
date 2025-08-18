@extends('layouts.app')

@section('title', 'My Products')

@section('content')
<div class="glass-effect rounded-2xl shadow-2xl p-8 w-full">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">My Products</h1>
            <p class="text-blue-100 text-lg">Manage your product inventory and track performance</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('seller.products.create') }}"
               class="bg-white text-blue-600 font-bold py-3 px-6 rounded-lg hover:bg-blue-50 transition duration-200 card-hover text-center">
                <i class="fas fa-plus mr-2"></i>Add New Product
            </a>
            <a href="{{ route('seller.dashboard') }}"
               class="bg-blue-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-600 transition duration-200 card-hover text-center">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <div class="text-3xl font-bold text-white mb-2">{{ $products->count() }}</div>
            <div class="text-blue-100">Total Products</div>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <div class="text-3xl font-bold text-green-300 mb-2">{{ $products->where('status', 'active')->count() }}</div>
            <div class="text-blue-100">Active Products</div>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <div class="text-3xl font-bold text-yellow-300 mb-2">{{ $products->where('status', 'inactive')->count() }}</div>
            <div class="text-blue-100">Inactive Products</div>
        </div>
        <div class="bg-white/10 rounded-xl p-6 text-center card-hover">
            <div class="text-3xl font-bold text-blue-300 mb-2">${{ number_format($products->sum('price'), 2) }}</div>
            <div class="text-blue-100">Total Value</div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-xl">
            <p class="text-green-200 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </p>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="mb-6 flex flex-col lg:flex-row gap-4">
        <div class="flex-1">
            <div class="relative">
                <input type="text"
                       placeholder="Search products..."
                       class="w-full px-4 py-3 pl-10 bg-white/10 border border-white/20 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-200"></i>
            </div>
        </div>
        <select class="px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200">
            <option value="">All Categories</option>
            <option value="electronics">Electronics</option>
            <option value="clothing">Clothing</option>
            <option value="books">Books</option>
            <option value="home">Home & Garden</option>
            <option value="sports">Sports</option>
        </select>
        <select class="px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition duration-200">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <!-- Products Table -->
    <div class="bg-white/5 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-white/10">
                    <tr>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                            <i class="fas fa-image mr-2"></i>Product
                        </th>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                            <i class="fas fa-list mr-2"></i>Category
                        </th>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                            <i class="fas fa-dollar-sign mr-2"></i>Price
                        </th>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                            <i class="fas fa-chart-line mr-2"></i>Status
                        </th>
                        <th class="px-8 py-4 text-left text-sm font-semibold text-blue-100 uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Created
                        </th>
                        <th class="px-8 py-4 text-center text-sm font-semibold text-blue-100 uppercase tracking-wider">
                            <i class="fas fa-cogs mr-2"></i>Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($products as $product)
                        <tr class="hover:bg-white/5 transition duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-lg bg-blue-500 flex items-center justify-center">
                                            <i class="fas fa-box text-white"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-lg font-medium text-white">{{ $product->name }}</div>
                                        <div class="text-sm text-blue-200">ID: #{{ $product->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm font-medium bg-blue-500/20 text-blue-200 rounded-full">
                                    Electronics
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-lg font-semibold text-white">${{ number_format($product->price, 2) }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full
                                    {{ $product->status === 'active' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                                    <i class="fas {{ $product->status === 'active' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-blue-200">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ now()->subDays(rand(1, 30))->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('seller.products.show', $product->id) }}"
                                       class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg transition duration-200 card-hover"
                                       title="View Product">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('seller.products.edit', $product->id) }}"
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-lg transition duration-200 card-hover"
                                       title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('seller.products.destroy', $product->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition duration-200 card-hover"
                                                title="Delete Product"
                                                onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center">
                                <div class="text-blue-200">
                                    <i class="fas fa-box-open text-6xl mb-4 opacity-50"></i>
                                    <h3 class="text-xl font-medium mb-2">No products found</h3>
                                    <p class="mb-4">Start building your inventory by adding your first product.</p>
                                    <a href="{{ route('seller.products.create') }}"
                                       class="bg-white text-blue-600 font-bold py-2 px-4 rounded-lg hover:bg-blue-50 transition duration-200 inline-block">
                                        <i class="fas fa-plus mr-2"></i>Create your first product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination (if needed) -->
    <div class="mt-8 flex justify-between items-center">
        <div class="text-blue-200 text-sm">
            Showing {{ $products->count() }} of {{ $products->count() }} products
        </div>
        <div class="flex space-x-2">
            <!-- Pagination buttons would go here -->
            <button class="px-4 py-2 bg-white/10 text-blue-200 rounded-lg hover:bg-white/20 transition duration-200">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-4 py-2 bg-blue-500 text-white rounded-lg">1</button>
            <button class="px-4 py-2 bg-white/10 text-blue-200 rounded-lg hover:bg-white/20 transition duration-200">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>
@endsection
