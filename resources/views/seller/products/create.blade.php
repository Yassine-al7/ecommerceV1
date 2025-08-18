@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="glass-effect rounded-2xl shadow-2xl p-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-white rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-plus text-2xl text-indigo-600"></i>
        </div>
        <h2 class="text-3xl font-bold text-white">Create New Product</h2>
        <p class="text-gray-200 mt-2">Add a new product to your inventory</p>
    </div>

    <!-- Product Form -->
    <form method="POST" action="{{ route('seller.products.store') }}" class="space-y-6">
        @csrf

        <!-- Product Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-200 mb-2">
                <i class="fas fa-tag mr-2"></i>Product Name
            </label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name') }}"
                   required
                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                   placeholder="Enter product name">
            @error('name')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-200 mb-2">
                <i class="fas fa-align-left mr-2"></i>Description
            </label>
            <textarea name="description"
                      id="description"
                      rows="4"
                      required
                      class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                      placeholder="Enter product description">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Price -->
        <div>
            <label for="price" class="block text-sm font-medium text-gray-200 mb-2">
                <i class="fas fa-dollar-sign mr-2"></i>Price
            </label>
            <input type="number"
                   name="price"
                   id="price"
                   value="{{ old('price') }}"
                   step="0.01"
                   min="0"
                   required
                   class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200"
                   placeholder="0.00">
            @error('price')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Category -->
        <div>
            <label for="category" class="block text-sm font-medium text-gray-200 mb-2">
                <i class="fas fa-list mr-2"></i>Category
            </label>
            <select name="category"
                    id="category"
                    required
                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition duration-200">
                <option value="">Select a category</option>
                <option value="electronics" {{ old('category') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                <option value="clothing" {{ old('category') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                <option value="books" {{ old('category') == 'books' ? 'selected' : '' }}>Books</option>
                <option value="home" {{ old('category') == 'home' ? 'selected' : '' }}>Home & Garden</option>
                <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>Sports</option>
                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('category')
                <p class="mt-2 text-sm text-red-300">
                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex space-x-4">
            <button type="submit"
                    class="flex-1 bg-white text-indigo-600 font-bold py-3 px-4 rounded-lg hover:bg-gray-100 transition duration-200">
                <i class="fas fa-save mr-2"></i>Create Product
            </button>
            <a href="{{ route('seller.products.index') }}"
               class="flex-1 bg-white/20 text-white font-bold py-3 px-4 rounded-lg hover:bg-white/30 transition duration-200 text-center">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection
