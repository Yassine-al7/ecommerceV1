<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')

    @section('content')
    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold">Manage Products</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Add New Product</a>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">ID</th>
                    <th class="py-2 px-4 border-b">Name</th>
                    <th class="py-2 px-4 border-b">Price</th>
                    <th class="py-2 px-4 border-b">Stock</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $product->id }}</td>
                    <td class="py-2 px-4 border-b">{{ $product->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $product->price }}</td>
                    <td class="py-2 px-4 border-b">{{ $product->stock }}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
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
