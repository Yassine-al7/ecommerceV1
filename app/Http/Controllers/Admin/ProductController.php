<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        // 🆕 FORCER LE RECHARGEMENT DES DONNÉES DEPUIS LA BASE
        $products = Product::with(['category', 'assignedUsers'])->get()->fresh();

        Log::info("🔄 Chargement de la liste des produits:", [
            'nombre_produits' => $products->count(),
            'timestamp' => now()->toDateTimeString()
        ]);

        // 🆕 FILTRER LES COULEURS AVEC STOCK = 0 POUR L'AFFICHAGE
        $products->each(function ($product) {
            // Utiliser directement les accesseurs du modèle (déjà décodés)
            $stockCouleurs = $product->stock_couleurs;
            $couleurs = $product->couleur;

            Log::info("🔍 Analyse du produit {$product->name}:", [
                'id' => $product->id,
                'stock_couleurs_type' => gettype($stockCouleurs),
                'stock_couleurs_count' => is_array($stockCouleurs) ? count($stockCouleurs) : 'N/A',
                'couleurs_type' => gettype($couleurs),
                'couleurs_count' => is_array($couleurs) ? count($couleurs) : 'N/A'
            ]);

            if (is_array($stockCouleurs) && is_array($couleurs) && !empty($stockCouleurs)) {
                $couleursFiltrees = [];
                $stockCouleursFiltres = [];

                // Log détaillé de chaque couleur et son stock
                foreach ($stockCouleurs as $index => $stock) {
                    Log::info("  📊 Couleur {$index}: {$stock['name']} = {$stock['quantity']} unités");

                    if ($stock['quantity'] > 0) {
                        // Conserver la couleur et son stock
                        $stockCouleursFiltres[] = $stock;

                        // Trouver la couleur correspondante
                        if (isset($couleurs[$index])) {
                            $couleursFiltrees[] = $couleurs[$index];
                        }
                    } else {
                        Log::info("  ❌ Couleur {$stock['name']} filtrée (stock ≤ 0)");
                    }
                }

                // Mettre à jour les attributs du produit pour l'affichage
                $product->couleur_filtree = $couleursFiltrees;
                $product->stock_couleurs_filtre = $stockCouleursFiltres;

                Log::info("🎨 Filtrage des couleurs pour {$product->name}:", [
                    'couleurs_originales' => count($couleurs),
                    'couleurs_filtrees' => count($couleursFiltrees),
                    'stock_original' => count($stockCouleurs),
                    'stock_filtre' => count($stockCouleursFiltres),
                    'couleurs_filtrees' => $couleursFiltrees,
                    'stock_filtres' => $stockCouleursFiltres
                ]);
            } else {
                Log::warning("⚠️ Produit {$product->name} sans données de couleurs valides", [
                    'stock_couleurs' => $stockCouleurs,
                    'couleurs' => $couleurs
                ]);
            }
        });

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

        // Traiter les couleurs avec leurs valeurs hexadécimales et stocks
        $couleurs = $request->input('couleurs', []);
        $couleursHex = $request->input('couleurs_hex', []);
        $couleursPersonnalisees = $request->input('couleurs_personnalisees', []);
        $stockCouleurs = [];

        // Créer un mapping couleur-hex-stock pour la sauvegarde
        $couleursWithHex = [];

        // Traiter d'abord les couleurs prédéfinies
        foreach ($couleurs as $index => $couleur) {
            $hex = $couleursHex[$index] ?? null;
            $stock = $request->input("stock_couleur_{$index}", 0);

            if ($hex) {
                $couleursWithHex[] = [
                    'name' => $couleur,
                    'hex' => $hex
                ];
            } else {
                $couleursWithHex[] = $couleur;
            }

            // Stocker le stock par couleur
            $stockCouleurs[] = [
                'name' => $couleur,
                'quantity' => (int) $stock
            ];
        }

        // Traiter ensuite les couleurs personnalisées
        foreach ($couleursPersonnalisees as $index => $couleur) {
            $stock = $request->input("stock_couleur_custom_{$index}", 0);

            // Ajouter la couleur personnalisée sans hex (sera généré automatiquement)
            $couleursWithHex[] = $couleur;

            // Stocker le stock par couleur
            $stockCouleurs[] = [
                'name' => $couleur,
                'quantity' => (int) $stock
            ];
        }

        // Convertir les couleurs en JSON (pour stockage en base)
        $data['couleur'] = json_encode($couleursWithHex);
        $data['stock_couleurs'] = json_encode($stockCouleurs);

        // Convertir les tailles en JSON - pour les accessoires, utiliser un tableau vide
        if ($isAccessoire) {
            $data['tailles'] = json_encode([]);
        } else {
            $data['tailles'] = json_encode($data['tailles'] ?? []);
        }

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
        Log::info('Update Product - Catégorie détectée:', [
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

        // FUSION INTELLIGENTE : Préserver les couleurs existantes avec leurs hex
        // Utiliser directement les attributs (déjà décodés par les casts/accesseurs)
        $existingColors = $product->couleur ?: [];
        $couleurs = $request->input('couleurs', []);
        $couleursHex = $request->input('couleurs_hex', []);
        $couleursPersonnalisees = $request->input('couleurs_personnalisees', []);

        // Utiliser la méthode de fusion intelligente
        $mergedData = $this->mergeColorsIntelligently($existingColors, $couleurs, $couleursHex, $couleursPersonnalisees);

        $couleursWithHex = $mergedData['colors'];
        $stockCouleurs = $mergedData['stock'];

        // Convertir les couleurs en JSON (pour stockage en base)
        $data['couleur'] = json_encode($couleursWithHex);
        $data['stock_couleurs'] = json_encode($stockCouleurs);

        // 🆕 RECALCULER CORRECTEMENT LE STOCK TOTAL
        $totalStock = array_sum(array_column($stockCouleurs, 'quantity'));
        $data['quantite_stock'] = $totalStock;

        Log::info('Update Product - Stock recalculé:', [
            'ancien_stock' => $product->quantite_stock,
            'nouveau_stock' => $totalStock,
            'couleurs_traitees' => count($couleursWithHex),
            'stock_par_couleur' => $stockCouleurs
        ]);

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

    /**
     * Fusionner intelligemment les couleurs existantes avec les nouvelles
     * Préserve les valeurs hexadécimales existantes et évite les doublons
     * Recalcule correctement le stock total en tenant compte des suppressions
     */
    private function mergeColorsIntelligently($existingColors, $newColors, $newColorsHex, $newCustomColors)
    {
        $mergedColors = [];
        $mergedStock = [];
        $processedColors = []; // Pour éviter les doublons

        // 1. Traiter d'abord les couleurs prédéfinies
        foreach ($newColors as $index => $couleur) {
            $hex = $newColorsHex[$index] ?? null;
            $stock = request()->input("stock_couleur_{$index}", 0);

            // Chercher si cette couleur existe déjà avec son hex
            $existingColor = $this->findExistingColor($existingColors, $couleur);

            if ($existingColor && isset($existingColor['hex'])) {
                // Garder l'hex existant
                $mergedColors[] = [
                    'name' => $couleur,
                    'hex' => $existingColor['hex']
                ];
            } else {
                // Utiliser le nouvel hex ou null
                $mergedColors[] = [
                    'name' => $couleur,
                    'hex' => $hex
                ];
            }

            // Stocker le stock par couleur
            $mergedStock[] = [
                'name' => $couleur,
                'quantity' => (int) $stock
            ];

            $processedColors[] = strtolower($couleur);
        }

        // 2. Traiter ensuite les couleurs personnalisées (AVOIDING DUPLICATES)
        foreach ($newCustomColors as $index => $couleur) {
            $stock = request()->input("stock_couleur_custom_{$index}", 0);

            // VÉRIFIER SI CETTE COULEUR PERSONNALISÉE EXISTE DÉJÀ
            $existingColor = $this->findExistingColor($existingColors, $couleur);

            // Si la couleur existe déjà, METTRE À JOUR le stock au lieu de dupliquer
            if ($existingColor) {
                // Chercher l'index dans le tableau fusionné pour mettre à jour le stock
                $stockIndex = $this->findStockIndex($mergedStock, $couleur);

                if ($stockIndex !== false) {
                    // Mettre à jour le stock existant
                    $mergedStock[$stockIndex]['quantity'] = (int) $stock;
                } else {
                    // Ajouter le stock si pas trouvé (cas rare)
                    $mergedStock[] = [
                        'name' => $couleur,
                        'quantity' => (int) $stock
                    ];
                }

                // Ajouter la couleur avec son hex existant (si elle n'est pas déjà dans mergedColors)
                if (!in_array(strtolower($couleur), $processedColors)) {
                    if (isset($existingColor['hex'])) {
                        $mergedColors[] = [
                            'name' => $couleur,
                            'hex' => $existingColor['hex']
                        ];
                    } else {
                        $mergedColors[] = $couleur;
                    }
                    $processedColors[] = strtolower($couleur);
                }
            } else {
                // Nouvelle couleur personnalisée - l'ajouter normalement
                if (!in_array(strtolower($couleur), $processedColors)) {
                    $mergedColors[] = $couleur;
                    $mergedStock[] = [
                        'name' => $couleur,
                        'quantity' => (int) $stock
                    ];
                    $processedColors[] = strtolower($couleur);
                }
            }
        }

        // 🆕 3. SUPPRESSION AUTOMATIQUE DES COULEURS PERSONNALISÉES AVEC STOCK ≤ 0
        $colorsToRemove = [];
        $stockToRemove = [];

        foreach ($mergedStock as $index => $stock) {
            if ($stock['quantity'] <= 0) {
                $colorName = $stock['name'];

                // Vérifier si c'est une couleur personnalisée (pas dans les couleurs prédéfinies)
                $isCustomColor = !in_array(strtolower($colorName), array_map('strtolower', $newColors));

                if ($isCustomColor) {
                    $colorsToRemove[] = $colorName;
                    $stockToRemove[] = $index;

                    Log::info("🗑️ Couleur personnalisée supprimée automatiquement: {$colorName} (stock: {$stock['quantity']})");
                }
            }
        }

        // Supprimer les couleurs et stocks avec stock ≤ 0
        if (!empty($colorsToRemove)) {
            // Supprimer les couleurs
            $mergedColors = array_filter($mergedColors, function($color) use ($colorsToRemove) {
                $colorName = is_array($color) ? $color['name'] : $color;
                return !in_array($colorName, $colorsToRemove);
            });

            // Supprimer les stocks (en ordre décroissant pour éviter les problèmes d'index)
            rsort($stockToRemove);
            foreach ($stockToRemove as $index) {
                unset($mergedStock[$index]);
            }

            // Réindexer le tableau de stock
            $mergedStock = array_values($mergedStock);

            Log::info('🗑️ Nettoyage automatique effectué:', [
                'couleurs_supprimees' => $colorsToRemove,
                'stock_supprime' => $stockToRemove,
                'couleurs_restantes' => count($mergedColors),
                'stock_restant' => count($mergedStock)
            ]);
        }

        // 🆕 4. VÉRIFICATION ET LOGS DE DEBUG FINAUX
        $totalStock = array_sum(array_column($mergedStock, 'quantity'));

        Log::info('Fusion intelligente des couleurs - Debug final:', [
            'existing_colors_count' => count($existingColors),
            'new_colors_count' => count($newColors),
            'new_custom_colors_count' => count($newCustomColors),
            'merged_colors_count' => count($mergedColors),
            'merged_stock_count' => count($mergedStock),
            'total_stock_calculated' => $totalStock,
            'processed_colors' => $processedColors,
            'merged_colors' => $mergedColors,
            'merged_stock' => $mergedStock,
            'colors_removed' => $colorsToRemove ?? [],
            'stock_removed' => $stockToRemove ?? []
        ]);

        return [
            'colors' => $mergedColors,
            'stock' => $mergedStock
        ];
    }

    /**
     * Trouver l'index d'une couleur dans le tableau de stock
     */
    private function findStockIndex($stockArray, $colorName)
    {
        foreach ($stockArray as $index => $stock) {
            if (isset($stock['name']) && strtolower($stock['name']) === strtolower($colorName)) {
                return $index;
            }
        }
        return false;
    }

    /**
     * Trouver une couleur existante par son nom
     */
    private function findExistingColor($existingColors, $colorName)
    {
        if (!$existingColors || !is_array($existingColors)) {
            return null;
        }

        foreach ($existingColors as $existingColor) {
            if (is_array($existingColor) && isset($existingColor['name']) && $existingColor['name'] === $colorName) {
                return $existingColor;
            } elseif (is_string($existingColor) && $existingColor === $colorName) {
                return ['name' => $colorName];
            }
        }

        return null;
    }
}
