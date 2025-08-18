<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products', compact('products'));
    }

    public function create()
    {
        $categories = \DB::table('categories')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'couleur' => 'required|string|max:100',
            'tailles' => 'nullable|string', // comma-separated sizes
            'image' => 'nullable|string',
            'quantite_stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'nullable|numeric',
            'prix_vente' => 'nullable|numeric',
        ]);

        $payload = $validated;
        if (!empty($validated['tailles'])) {
            $sizes = array_values(array_filter(array_map('trim', explode(',', $validated['tailles']))));
            $payload['tailles'] = json_encode($sizes);
        } else {
            $payload['tailles'] = json_encode([]);
        }

        Product::create($payload);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = \DB::table('categories')->get();
        return view('admin.products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'couleur' => 'required|string|max:100',
            'tailles' => 'nullable|string',
            'image' => 'nullable|string',
            'quantite_stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'nullable|numeric',
            'prix_vente' => 'nullable|numeric',
        ]);

        $payload = $validated;
        if (array_key_exists('tailles', $validated)) {
            $sizes = array_values(array_filter(array_map('trim', explode(',', $validated['tailles']))));
            $payload['tailles'] = json_encode($sizes);
        }

        $product->update($payload);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function assignForm(Product $product)
    {
        $sellers = User::where('role', 'seller')->get();
        return view('admin.products.assign', compact('product', 'sellers'));
    }

    public function assignStore(Request $request, Product $product)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'prix_admin' => 'nullable|numeric',
            'prix_vente' => 'nullable|numeric',
            'visible' => 'nullable|boolean',
        ]);

        $product->assignedSellers()->syncWithoutDetaching([
            $data['user_id'] => [
                'prix_admin' => $data['prix_admin'] ?? null,
                'prix_vente' => $data['prix_vente'] ?? null,
                'visible' => $data['visible'] ?? true,
            ],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produit assigné au vendeur.');
    }
}
