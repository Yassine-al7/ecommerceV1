<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold mb-4">Manage Your Orders</h1>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Order ID</th>
                    <th class="py-2 px-4 border-b">Customer Name</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Total Price</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $order->id }}</td>
                    <td class="py-2 px-4 border-b">{{ $order->customer_name }}</td>
                    <td class="py-2 px-4 border-b">{{ $order->status }}</td>
                    <td class="py-2 px-4 border-b">{{ $order->total_price }} €</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('seller.orders.show', $order->id) }}" class="text-blue-500">View</a>
                        <a href="{{ route('seller.orders.edit', $order->id) }}" class="text-yellow-500">Edit</a>
                        <form action="{{ route('seller.orders.destroy', $order->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            <a href="{{ route('seller.orders.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add New Order</a>
        </div>
    </div>
    @endsection
</body>
</html>
