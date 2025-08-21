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
        $products = Product::with(['category', 'assignedUsers'])->get();
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
            'couleurs' => 'required|array|min:1',
            'tailles' => 'required|array|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantite_stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ]);

        // Convertir les couleurs en JSON (pour stockage en base)
        $data['couleur'] = json_encode($data['couleurs']);

        // Convertir les tailles en JSON
        $data['tailles'] = json_encode($data['tailles']);

        // Note: vendeur_id n'est plus utilisé - nous utilisons la table pivot product_user

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
            'couleurs' => 'required|array|min:1',
            'tailles' => 'required|array|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantite_stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ]);

        // Convertir les couleurs en JSON (pour stockage en base)
        $data['couleur'] = json_encode($data['couleurs']);

        // Convertir les tailles en JSON
        $data['tailles'] = json_encode($data['tailles']);

        // Note: vendeur_id n'est plus utilisé - nous utilisons la table pivot product_user

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
            'selected_sellers' => 'required|array|min:1',
            'selected_sellers.*' => 'exists:users,id',
            'prix_admin' => 'nullable|numeric',
            'prix_vente' => 'nullable|numeric',
            'visible' => 'nullable|boolean',
            'action' => 'required|in:assign,remove',
        ]);

        $action = $data['action'];
        $selectedSellers = $data['selected_sellers'];

        // Utiliser les prix du produit si non spécifiés
        $prixAdmin = $data['prix_admin'] ?? $product->prix_admin;
        $prixVente = $data['prix_vente'] ?? $product->prix_vente;
        $visible = $data['visible'] ?? true;

        if ($action === 'assign') {
            // Préparer les données pour l'assignation
            $assignData = [];
            foreach ($selectedSellers as $sellerId) {
                $assignData[$sellerId] = [
                    'prix_admin' => $prixAdmin,
                    'prix_vente' => $prixVente,
                    'visible' => $visible,
                ];
            }

            // Assigner les vendeurs sélectionnés
            $product->assignedSellers()->syncWithoutDetaching($assignData);

            $message = count($selectedSellers) > 1
                ? count($selectedSellers) . ' vendeurs assignés avec succès !'
                : 'Vendeur assigné avec succès !';

            return redirect()->back()->with('success', $message);

        } else { // action === 'remove'
            // Désassigner les vendeurs sélectionnés
            $product->assignedSellers()->detach($selectedSellers);

            $message = count($selectedSellers) > 1
                ? count($selectedSellers) . ' vendeurs désassignés avec succès !'
                : 'Vendeur désassigné avec succès !';

            return redirect()->back()->with('success', $message);
        }
    }
}
