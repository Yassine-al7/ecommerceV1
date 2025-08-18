<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Admin Panel</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold mb-4">Sales Statistics</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded shadow">
                <h2 class="font-semibold">Total Orders</h2>
                <p class="text-lg">{{ $totalOrders }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="font-semibold">Total Sellers</h2>
                <p class="text-lg">{{ $totalSellers }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="font-semibold">Best Seller</h2>
                <p class="text-lg">{{ $bestSeller->name ?? 'N/A' }}</p>
            </div>
        </div>

        <h2 class="text-xl font-bold mt-6">Products Sold</h2>
        <table class="min-w-full bg-white border border-gray-300 mt-4">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Product Name</th>
                    <th class="border px-4 py-2">Units Sold</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td class="border px-4 py-2">{{ $product->name }}</td>
                    <td class="border px-4 py-2">{{ $product->units_sold }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection
</body>
</html>
