<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'couleur' => 'required|string',
            'couleur_text' => 'nullable|string',
            'tailles' => 'required|array|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantite_stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ]);

        // Utiliser le nom de couleur si fourni, sinon la valeur hex
        $data['couleur'] = $data['couleur_text'] ?: $data['couleur'];

        // Convertir les tailles en JSON
        $data['tailles'] = json_encode($data['tailles']);

        // Définir vendeur_id à null pour les produits créés par l'admin
        $data['vendeur_id'] = null;

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = '/storage/' . $imagePath;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Produit créé avec succès!');
    }

    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'couleur' => 'required|string',
            'couleur_text' => 'nullable|string',
            'tailles' => 'required|array|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantite_stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ]);

        // Utiliser le nom de couleur si fourni, sinon la valeur hex
        $data['couleur'] = $data['couleur_text'] ?: $data['couleur'];

        // Convertir les tailles en JSON
        $data['tailles'] = json_encode($data['tailles']);

        // Garder vendeur_id inchangé lors de la mise à jour
        unset($data['vendeur_id']);

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = '/storage/' . $imagePath;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour avec succès!');
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
