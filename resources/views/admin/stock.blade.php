<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold">Stock Management</h1>
        <a href="{{ route('admin.stock.create') }}" class="btn btn-primary mb-4">Add New Stock</a>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Product</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Color</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Size</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Quantity</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Supplier</th>
                    <th class="border-b-2 border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                <tr>
                    <td class="border-b border-gray-300 px-4 py-2">{{ $stock->product->name }}</td>
                    <td class="border-b border-gray-300 px-4 py-2">{{ $stock->color }}</td>
                    <td class="border-b border-gray-300 px-4 py-2">{{ $stock->size }}</td>
                    <td class="border-b border-gray-300 px-4 py-2">{{ $stock->quantity }}</td>
                    <td class="border-b border-gray-300 px-4 py-2">{{ $stock->supplier->name }}</td>
                    <td class="border-b border-gray-300 px-4 py-2">
                        <a href="{{ route('admin.stock.edit', $stock->id) }}" class="text-blue-500">Edit</a>
                        <form action="{{ route('admin.stock.destroy', $stock->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection
</body>
</html>
