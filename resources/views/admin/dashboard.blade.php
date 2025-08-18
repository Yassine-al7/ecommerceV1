<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        <div class="mt-4">
            <h2 class="text-xl">Total Orders: {{ $totalOrders }}</h2>
            <h2 class="text-xl">Total Sellers: {{ $totalSellers }}</h2>
        </div>
        <div class="mt-6">
            <h3 class="text-lg font-semibold">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4 mt-4">
                <a href="{{ route('admin.products.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Manage Products
                </a>
                <a href="{{ route('admin.sellers.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Manage Sellers
                </a>
                <a href="{{ route('admin.statistics.index') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    View Statistics
                </a>
                <a href="{{ route('admin.stock.index') }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                    Manage Stock
                </a>
            </div>
        </div>
    </div>
    @endsection
</body>
</html>
