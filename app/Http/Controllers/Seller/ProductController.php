<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the seller's products.
     */
    public function index()
    {
        // For now, return a simple view with dummy data
        $products = collect([
            (object) ['id' => 1, 'name' => 'Sample Product 1', 'price' => 29.99, 'status' => 'active'],
            (object) ['id' => 2, 'name' => 'Sample Product 2', 'price' => 49.99, 'status' => 'inactive'],
        ]);

        return view('seller.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('seller.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
        ]);

        // For now, just simulate storing the product
        return redirect()->route('seller.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        // Simulate finding a product
        $product = (object) [
            'id' => $id,
            'name' => 'Sample Product ' . $id,
            'description' => 'This is a sample product description.',
            'price' => 29.99,
            'category' => 'Electronics',
            'status' => 'active'
        ];

        return view('seller.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        // Simulate finding a product
        $product = (object) [
            'id' => $id,
            'name' => 'Sample Product ' . $id,
            'description' => 'This is a sample product description.',
            'price' => 29.99,
            'category' => 'Electronics',
            'status' => 'active'
        ];

        return view('seller.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
        ]);

        // For now, just simulate updating the product
        return redirect()->route('seller.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        // For now, just simulate deleting the product
        return redirect()->route('seller.products.index')->with('success', 'Product deleted successfully.');
    }
}
