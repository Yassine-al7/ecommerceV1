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
        // Récupérer la catégorie pour vérifier si c'est un accessoire
        $categorie = \App\Models\Category::find($request->categorie_id);
        $isAccessoire = $categorie && strtolower($categorie->name) === 'accessoire';

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'couleurs' => 'required|array|min:1',
            'couleurs_hex' => 'array',
            'stock_couleurs' => 'nullable|array',
            'tailles' => $isAccessoire ? 'nullable|array' : 'required|array|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantite_stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ]);

        // Traiter les couleurs avec leurs valeurs hexadécimales
        $couleurs = $request->input('couleurs', []);
        $couleursHex = $request->input('couleurs_hex', []);

        // Créer un mapping couleur-hex pour la sauvegarde
        $couleursWithHex = [];
        foreach ($couleurs as $index => $couleur) {
            $hex = $couleursHex[$index] ?? null;
            if ($hex) {
                $couleursWithHex[] = [
                    'name' => $couleur,
                    'hex' => $hex
                ];
            } else {
                $couleursWithHex[] = $couleur; // Fallback si pas de hex
            }
        }

        // Convertir les couleurs en JSON (pour stockage en base)
        $data['couleur'] = json_encode($couleursWithHex);

        // Convertir les tailles en JSON
        $data['tailles'] = json_encode($data['tailles']);

        // Note: vendeur_id n'est plus utilisé - nous utilisons la table pivot product_user

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = '/storage/' . $imagePath;
        } else {
            // Si aucune image n'est fournie, utiliser une image par défaut
            $data['image'] = '/storage/products/default-product.svg';
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
        // Récupérer la catégorie pour vérifier si c'est un accessoire
        $categorie = \App\Models\Category::find($request->categorie_id);
        $isAccessoire = $categorie && (
            strtolower($categorie->name) === 'accessoire' ||
            strtolower($categorie->name) === 'accessoires' ||
            strpos(strtolower($categorie->name), 'accessoire') !== false
        );

        // Debug pour voir la catégorie
        \Log::info('Update Product - Catégorie détectée:', [
            'categorie_id' => $request->categorie_id,
            'categorie_name' => $categorie ? $categorie->name : 'null',
            'is_accessoire' => $isAccessoire,
            'tailles_sent' => $request->has('tailles') ? 'oui' : 'non',
            'tailles_data' => $request->input('tailles', [])
        ]);

        $validationRules = [
            'name' => 'required|string|max:255',
            'couleurs' => 'required|array|min:1',
            'couleurs_hex' => 'array',
            'stock_couleurs' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantite_stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
        ];

        // Ajouter la validation des tailles conditionnellement
        if (!$isAccessoire) {
            $validationRules['tailles'] = 'required|array|min:1';
        } else {
            $validationRules['tailles'] = 'nullable|array';
        }

        $data = $request->validate($validationRules);

        // Traiter les couleurs avec leurs valeurs hexadécimales
        $couleurs = $request->input('couleurs', []);
        $couleursHex = $request->input('couleurs_hex', []);

        // Créer un mapping couleur-hex pour la sauvegarde
        $couleursWithHex = [];
        foreach ($couleurs as $index => $couleur) {
            $hex = $couleursHex[$index] ?? null;
            if ($hex) {
                $couleursWithHex[] = [
                    'name' => $couleur,
                    'hex' => $hex
                ];
            } else {
                $couleursWithHex[] = $couleur; // Fallback si pas de hex
            }
        }

        // Convertir les couleurs en JSON (pour stockage en base)
        $data['couleur'] = json_encode($couleursWithHex);

        // Convertir les tailles en JSON (nullable pour les accessoires)
        if (isset($data['tailles']) && !empty($data['tailles'])) {
            $data['tailles'] = json_encode($data['tailles']);
        } else {
            $data['tailles'] = null;
        }

        // Note: vendeur_id n'est plus utilisé - nous utilisons la table pivot product_user

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = '/storage/' . $imagePath;
        } else {
            // Si aucune nouvelle image n'est fournie, conserver l'image existante
            $data['image'] = $product->image ?? '/storage/products/default-product.svg';
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

        if ($action === 'assign') {
            // Assigner les vendeurs au produit
            foreach ($selectedSellers as $sellerId) {
                $product->assignedUsers()->syncWithoutDetaching([
                    $sellerId => [
                        'prix_admin' => $prixAdmin,
                        'prix_vente' => $prixVente,
                        'visible' => $data['visible'] ?? true,
                    ]
                ]);
            }
            $message = 'Vendeurs assignés avec succès!';
        } else {
            // Retirer les vendeurs du produit
            $product->assignedUsers()->detach($selectedSellers);
            $message = 'Vendeurs retirés avec succès!';
        }

        return redirect()->route('admin.products.index')->with('success', $message);
    }

    public function assignIndex(Product $product)
    {
        $assignedUsers = $product->assignedUsers()->withPivot(['prix_admin', 'prix_vente', 'visible'])->get();
        return view('admin.products.assignments', compact('product', 'assignedUsers'));
    }
}
