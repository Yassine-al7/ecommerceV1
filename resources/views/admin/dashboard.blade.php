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
            <h3 class="text-lg font-semibold">Statistics</h3>
            <ul>
                <li>Best Seller: {{ $bestSeller }}</li>
                <li>Number of Products per Seller: {{ $productsPerSeller }}</li>
                <li>Units Sold per Product: {{ $unitsSold }}</li>
            </ul>
        </div>
        <div class="mt-6">
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Manage Products</a>
            <a href="{{ route('admin.sellers.index') }}" class="btn btn-primary">Manage Sellers</a>
            <a href="{{ route('admin.statistics.index') }}" class="btn btn-primary">View Statistics</a>
            <a href="{{ route('admin.stock.index') }}" class="btn btn-primary">Manage Stock</a>
        </div>
    </div>
    @endsection
</body>
</html>
