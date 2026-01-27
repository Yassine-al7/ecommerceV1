@extends('layouts.app')

    @section('title', 'إدارة المنتجات')

    @php
    use App\Helpers\ColorHelper;
    @endphp

@section('content')
<div class="min-h-screen bg-gray-50 py-4 md:py-8">
    <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 text-center md:text-left">إدارة المنتجات</h1>
                <p class="text-gray-600 text-center md:text-left">قم بإدارة كتالوج المنتجات وقم بتعيينها للبائعين</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success mb-6">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Actions + Filtres -->
            <div class="bg-white rounded-lg shadow-lg p-4 md:p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-lg md:text-xl font-semibold text-gray-800 text-center md:text-left">قائمة المنتجات</h2>
                        <p class="text-gray-600 mt-1 text-center md:text-left">المجموع: {{ $products->count() }} منتج</p>
                    </div>
                    <div class="flex flex-col md:flex-row gap-3 md:items-center">
                        <form method="GET" action="{{ route('admin.products.index') }}" id="adminProductsFilter" class="flex gap-2 items-center">
                            <label class="text-sm text-gray-700">التصنيف</label>
                            <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">الكل</option>
                                @isset($categories)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            <button type="submit" class="btn bg-gray-100 hover:bg-gray-200">تطبيق</button>
                            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-800">إعادة تعيين</a>
                        </form>
                        <div class="actions-buttons">
                            <a href="{{ route('admin.products.create') }}" class="btn bg-blue-600 hover:bg-blue-700 text-white">
                                <i class="fas fa-plus mr-2"></i>إنشاء منتج
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($products->count() > 0)
                <!-- Grille des produits modernes -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <x-modern-product-card :product="$product" user-type="admin" />
                    @endforeach
                </div>
            @else
                <!-- État vide -->
                <div class="bg-white rounded-lg shadow-lg p-8 md:p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-box-open text-4xl md:text-6xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-medium text-gray-800 mb-2">لا توجد منتجات</h3>
                    <p class="text-gray-600 mb-6">ابدأ بإنشاء أول منتج لإثراء الكتالوج.</p>
                    <div class="actions-buttons justify-center">
                        <a href="{{ route('admin.products.create') }}"
                           class="btn bg-blue-600 hover:bg-blue-700 text-white">
                            <i class="fas fa-plus mr-2"></i>أنشئ أول منتج لك
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>
@endsection
