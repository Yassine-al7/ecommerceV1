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
    public function index(Request $request)
    {
        // 🆕 LISTE PRODUITS AVEC FILTRE CATÉGORIE (optionnel)
        $query = Product::with(['category', 'assignedUsers']);

        if ($request->filled('category')) {
            $categoryName = $request->input('category');
            $query->whereHas('category', function($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        // FORCER LE RECHARGEMENT DES DONNÉES DEPUIS LA BASE
        // Trier par les plus récents (nouveaux produits en premier)
        $products = $query->orderBy('created_at', 'desc')->get()->fresh();

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
                    // Vérifier que $stock est un array avec les bonnes clés
                    if (is_array($stock) && isset($stock['name']) && isset($stock['quantity'])) {
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
                    } else {
                        Log::warning("  ⚠️ Structure de stock invalide pour l'index {$index}: " . json_encode($stock));
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

        // Récupérer les catégories pour le filtre
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.products', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function createModern()
    {
        $categories = \App\Models\Category::all();
        // Return the final clean form
        return view('admin.products.create-final', compact('categories'));
    }



    public function store(Request $request)
    {
        // 1. HEX/STEALTH DECODING (If applicable)
        if ($request->filled('product_payload')) {
            try {
                $hex = $request->input('product_payload');
                if (ctype_xdigit($hex)) {
                    $json = hex2bin($hex);
                    $decoded = json_decode($json, true);
                    
                    if ($decoded) {
                        // Hydrate Request with decoded data
                        $request->merge([
                            'name' => $decoded['name'] ?? null,
                            'description' => $decoded['description'] ?? null,
                            'categorie_id' => $decoded['categorie_id'] ?? null,
                            'prix_admin' => $decoded['prix_admin'] ?? null,
                            'prix_vente' => $decoded['prix_vente'] ?? null,
                            'colors_json' => json_encode($decoded['colors'] ?? []),
                            'sizes_json' => json_encode($decoded['sizes'] ?? []),
                            'total_stock' => $decoded['total_stock'] ?? 0,
                            'uploaded_image_path' => $decoded['uploaded_image_path'] ?? null,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Hex decode error: ' . $e->getMessage());
            }
        }

        // 2. Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required',
            'prix_vente' => 'required',
            // Image validation is laxer because it might be pre-uploaded
            'image' => 'nullable', 
            'colors_json' => 'nullable', // Accepted as string or array
            'sizes_json' => 'nullable',
            'total_stock' => 'nullable'
        ]);

        // 3. Process Variants
        // Handle both JSON string (from Hex decode above) or direct array (if sent normally)
        $colorsInput = $request->input('colors_json', '[]');
        $colorsData = is_string($colorsInput) ? (json_decode($colorsInput, true) ?? []) : $colorsInput;

        $sizesInput = $request->input('sizes_json', '[]');
        $sizesData = is_string($sizesInput) ? (json_decode($sizesInput, true) ?? []) : $sizesInput;
        
        $couleursWithHex = [];
        $stockCouleurs = [];
        $formattedSizes = [];

        foreach ($colorsData as $color) {
            if (empty($color['name'])) continue;
            
            $name = $color['name'];
            $hex = $color['hex'] ?? '#cccccc';
            if (!str_starts_with($hex, '#')) $hex = '#' . $hex;
            
            $stock = (int)($color['stock'] ?? 0);

            $couleursWithHex[] = ['name' => $name, 'hex' => $hex];
            $stockCouleurs[] = ['name' => $name, 'quantity' => $stock];
        }

        foreach ($sizesData as $size) {
            $formattedSizes[] = $size;
        }

        // 4. Process Prices
        $prixAdminInput = $request->input('prix_admin');
        $prixAdminArray = [];
        
        if (is_numeric($prixAdminInput)) {
            $prixAdminArray = [(float)$prixAdminInput];
        } else {
            $parts = preg_split('/[\s,|-]+/', $prixAdminInput, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($parts as $p) {
                if (is_numeric($p)) $prixAdminArray[] = (float)$p;
            }
        }
        if (empty($prixAdminArray)) $prixAdminArray = [(float)$request->input('prix_vente')];
        $prixAdminMoyen = array_sum($prixAdminArray) / count($prixAdminArray);

        // 5. Handle Image (Prioritize pre-uploaded path)
        $imagePath = null;
        if ($request->filled('uploaded_image_path')) {
             $imagePath = $request->input('uploaded_image_path');
        } elseif ($request->hasFile('image')) {
            $imagePath = '/storage/' . $request->file('image')->store('products', 'public');
        }

        // 6. Create Data Array
        $productData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'categorie_id' => $validated['categorie_id'],
            'prix_vente' => $validated['prix_vente'],
            'prix_admin' => json_encode($prixAdminArray),
            'prix_admin_moyen' => $prixAdminMoyen,
            'image' => $imagePath,
            'couleur' => $couleursWithHex,
            'stock_couleurs' => $stockCouleurs,
            'tailles' => $formattedSizes,
            'quantite_stock' => (int)$request->input('total_stock', 0),
            'color_images' => []
        ];

        $product = Product::create($productData);

        // 7. Assign to Sellers
        $this->syncProductWithAllSellers($product);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'redirect' => route('admin.products.index')]);
        }
        return redirect()->route('admin.products.index')->with('success', 'Produit créé avec succès!');
    }

    /**
     * Handle standalone secure image upload
     */
    public function uploadImage(Request $request) 
    {
        if ($request->hasFile('image')) {
            try {
                $path = '/storage/' . $request->file('image')->store('products', 'public');
                return response()->json(['path' => $path]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return response()->json(['error' => 'No image file'], 400);
    }

    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function editModern(Product $product)
    {
        $categories = \App\Models\Category::all();
        // Pour l'édition, on pourrait créer un edit-new, mais pour l'instant voyons si create-new fonctionne
        return view('admin.products.edit-modern', compact('product', 'categories'));
    }


    public function update(Request $request, Product $product)
    {
        // 1. STEALTH DECODING (Ported from store)
        // This allows the Edit form to use the same secure hex payload to bypass WAFs
        if ($request->filled('product_payload')) {
            try {
                $hex = $request->input('product_payload');
                if (ctype_xdigit($hex)) {
                    $json = hex2bin($hex);
                    $decoded = json_decode($json, true);
                    
                    if ($decoded) {
                        // Hydrate Request with decoded data
                        $mergeData = [
                            'name' => $decoded['name'] ?? null,
                            'description' => $decoded['description'] ?? null,
                            'categorie_id' => $decoded['categorie_id'] ?? null,
                            'prix_admin' => $decoded['prix_admin'] ?? null,
                            'prix_vente' => $decoded['prix_vente'] ?? null,
                            'quantite_stock' => $decoded['total_stock'] ?? 0,
                            // Convert JS arrays to what our validation expects
                            // We will process these properly below
                            'colors_json' => json_encode($decoded['colors'] ?? []),
                            'sizes_json' => json_encode($decoded['sizes'] ?? []),
                        ];

                        // Handle Image - If new one sent in payload
                        if (!empty($decoded['uploaded_image_path'])) {
                            $mergeData['uploaded_image_path'] = $decoded['uploaded_image_path'];
                        }

                        $request->merge($mergeData);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Hex decode error in update: ' . $e->getMessage());
            }
        }

        // 2. Compatibility Layer: Convert JSON inputs from Stealth to "Standard" Request format
        // This makes the rest of the existing update() logic work without rewriting it all
        if ($request->filled('colors_json')) {
            $colorsData = json_decode($request->input('colors_json'), true) ?? [];
            $couleurs = [];
            $couleursHex = [];
            $stockFields = [];
            
            foreach ($colorsData as $i => $c) {
                $couleurs[] = $c['name'];
                $couleursHex[] = $c['hex'];
                $request->merge(["stock_couleur_{$i}" => $c['stock']]);
            }
            $request->merge([
                'couleurs' => $couleurs,
                'couleurs_hex' => $couleursHex
            ]);
        }

        if ($request->filled('sizes_json')) {
            $sizesData = json_decode($request->input('sizes_json'), true) ?? [];
            $request->merge(['tailles' => $sizesData]);
        }
        
        // Handle Secure Image Path Injection
        if ($request->filled('uploaded_image_path')) {
            // We can't inject a "File" object effortlessly, so we'll bypass the validation
            // and handle the path assignment manually later if it's set.
            // However, the existing logic checks $request->hasFile('image').
            // We will modify the logic below to check for this path too.
        }

        \Illuminate\Support\Facades\Log::info('Update request reached controller', [
            'product_id' => $product->id,
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'all_data_except_files' => $request->except(['image', 'color_images_*']),
            'files_count' => count($request->allFiles())
        ]);

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
            'hidden_colors' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480', // Augmenté à 20MB pour sécurité
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|string|min:1|regex:/^[\d\s,.-]+$/', // Accepte nombres, virgules, espaces, points, tirets
            'prix_vente' => 'required|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0', // Stock global obligatoire
        ];

        // Ajouter la validation des stocks par couleur seulement s'ils sont présents
        $couleurs = $request->input('couleurs', []);
        // Nouvelle méthode : décoder le JSON des couleurs personnalisées
        $customColorsJson = $request->input('custom_colors_json', '[]');
        $customColorsData = json_decode($customColorsJson, true) ?? [];

        // Vérifier si des champs de stock par couleur sont présents
        $hasStockFields = false;
        foreach ($request->all() as $key => $value) {
            // Check standard stock fields OR if we have custom colors payload
            if (str_starts_with($key, 'stock_couleur_') || !empty($customColorsData)) {
                $hasStockFields = true;
                break;
            }
        }

        // Ajouter la validation des stocks par couleur seulement s'ils sont présents
        if ($hasStockFields) {
            foreach ($couleurs as $index => $couleur) {
                if ($request->has("stock_couleur_{$index}")) {
                    $validationRules["stock_couleur_{$index}"] = 'required|integer|min:0';
                }
            }

            // Custom colors validation handled implicitly via data presence

        }

        // Ajouter la validation des tailles conditionnellement
        if (!$isAccessoire) {
            // Si c'est une modification et que le produit a déjà des tailles, permettre un tableau vide
            if ($product->tailles && !empty($product->tailles)) {
                $validationRules['tailles'] = 'nullable|array';
            } else {
                $validationRules['tailles'] = 'required|array|min:1';
            }
        } else {
            $validationRules['tailles'] = 'nullable|array';
        }

        // Debug: afficher les règles de validation et les données reçues
        Log::info('Validation rules:', $validationRules);
        Log::info('Request data:', $request->all());

        try {
            $data = $request->validate($validationRules);
            Log::info('Validation successful');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        // Traitement simplifié des couleurs et du stock
        $couleursHex = $request->input('couleurs_hex', []);

        // Construire les couleurs avec hex
        $couleursWithHex = [];
        $stockCouleurs = [];

        // Si des champs de stock par couleur sont présents, les traiter
        if ($hasStockFields) {
            // Traiter les couleurs prédéfinies
            foreach ($couleurs as $index => $couleur) {
                $hex = $couleursHex[$index] ?? '#cccccc';
                if (!str_starts_with($hex, '#')) $hex = '#' . $hex;
                $stock = $request->input("stock_couleur_{$index}", 0);

                $couleursWithHex[] = [
                    'name' => $couleur,
                    'hex' => $hex
                ];

                $stockCouleurs[] = [
                    'name' => $couleur,
                    'quantity' => (int) $stock
                ];
            }

            // Traiter les couleurs personnalisées via JSON
            foreach ($customColorsData as $customColor) {
                $name = $customColor['name'] ?? null;
                $hexRaw = $customColor['hex'] ?? 'cccccc';
                $stock = $customColor['stock'] ?? 0;
                
                if ($name) {
                    $hex = str_starts_with($hexRaw, '#') ? $hexRaw : '#' . $hexRaw;
    
                    $couleursWithHex[] = [
                        'name' => $name,
                        'hex' => $hex
                    ];
    
                    $stockCouleurs[] = [
                        'name' => $name,
                        'quantity' => (int) $stock
                    ];
                }
            }
        } else {
            // Si pas de champs de stock par couleur, garder les couleurs existantes
            $existingColors = $product->couleur ?? [];
            $existingStock = $product->stock_couleurs ?? [];

            if (is_string($existingColors)) {
                $existingColors = json_decode($existingColors, true) ?? [];
            }
            if (is_string($existingStock)) {
                $existingStock = json_decode($existingStock, true) ?? [];
            }

            $couleursWithHex = $existingColors;
            $stockCouleurs = $existingStock;
        }

        // Convertir les couleurs en JSON (pour stockage en base)
        $data['couleur'] = $couleursWithHex; // Laravel cast automatiquement en JSON
        $data['stock_couleurs'] = $stockCouleurs; // Laravel cast automatiquement en JSON

        // Traiter les couleurs masquées
        $hiddenColors = $request->input('hidden_colors', []);
        $data['hidden_colors'] = $hiddenColors; // Laravel cast automatiquement en JSON

        Log::info('Update Product - Couleurs masquées:', [
            'hidden_colors_input' => $hiddenColors,
            'hidden_colors_json' => $data['hidden_colors']
        ]);

        // Calculer le stock total à partir des stocks par couleur seulement s'ils sont présents
        if ($hasStockFields && !empty($stockCouleurs)) {
            $totalStock = array_sum(array_column($stockCouleurs, 'quantity'));
            $data['quantite_stock'] = $totalStock;
        } else {
            // Utiliser le quantite_stock du formulaire (déjà validé)
            $data['quantite_stock'] = $request->input('quantite_stock', $product->quantite_stock);
        }

        Log::info('Update Product - Stock par couleur mis à jour:', [
            'couleurs_traitees' => count($couleursWithHex),
            'stock_par_couleur' => $stockCouleurs,
            'quantite_stock_final' => $data['quantite_stock'],
            'has_stock_fields' => $hasStockFields
        ]);

        // Convertir les tailles en JSON (nullable pour les accessoires)
        if (isset($data['tailles']) && !empty($data['tailles'])) {
            $data['tailles'] = $data['tailles']; // Laravel cast automatiquement en JSON
        } else {
            // Si pas de tailles fournies, garder les tailles existantes
            $data['tailles'] = $product->tailles;
        }

        // Note: vendeur_id n'est plus utilisé - nous utilisons la table pivot product_user

        // Traiter les prix multiples pour prix_admin
        $prixAdminInput = $data['prix_admin'];
        $prixAdminArray = [];

        // Nettoyer et séparer les prix
        $prixCleaned = preg_replace('/[^\d,.\s-]/', '', $prixAdminInput); // Garder seulement chiffres, virgules, points, espaces, tirets
        $prixParts = preg_split('/[,;|\s-]+/', $prixCleaned); // Séparer par virgules, points-virgules, pipes, espaces, ou tirets

        foreach ($prixParts as $prix) {
            $prix = trim($prix);
            if (is_numeric($prix) && $prix > 0) {
                $prixAdminArray[] = (float) $prix;
            }
        }

        // Si aucun prix valide trouvé, utiliser le prix de vente
        if (empty($prixAdminArray)) {
            $prixAdminArray = [(float) $data['prix_vente']];
        }

        // Calculer le prix moyen pour l'assignation aux vendeurs
        $prixAdminMoyen = array_sum($prixAdminArray) / count($prixAdminArray);

        // Stocker les prix multiples en JSON
        $data['prix_admin'] = json_encode($prixAdminArray);
        $data['prix_admin_moyen'] = $prixAdminMoyen; // Prix moyen pour les assignations

        // Gérer l'upload d'image principale
        // Updated to support Stealth Path
        if ($request->filled('uploaded_image_path')) {
             // Supprimer l'ancienne image si elle existe et est différente
             $newPath = $request->input('uploaded_image_path');
             if ($product->image && $product->image !== $newPath && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image))) {
                // Delete old one? Maybe risky if shared.. keeping it safe for now.
                // Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
             }
             $data['image'] = $newPath;
        } elseif ($request->hasFile('image')) {
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

        // Les images par couleur ne sont plus utilisées selon la demande client
        $colorImages = $product->color_images ?: [];
        $data['color_images'] = $colorImages;

        // Si aucune image principale n'est fournie mais qu'il y a des images par couleur,
        // utiliser la première image comme image principale
        if ($data['image'] === '/storage/products/default-product.svg' && !empty($colorImages)) {
            $firstColorImages = $colorImages[0]['images'] ?? [];
            if (!empty($firstColorImages)) {
                $data['image'] = $firstColorImages[0];
            }
        }


        // Le stock total est maintenant géré dans la logique ci-dessus

        $product->update($data);

        // Synchroniser les assignations avec tous les vendeurs existants
        $this->syncProductWithAllSellers($product);

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
        $assignedSellers = $product->assignedSellers;
        return view('admin.products.assign', compact('product', 'sellers', 'assignedSellers'));
    }

    public function assignStore(Request $request, Product $product)
    {
        $data = $request->validate([
            'selected_sellers' => 'required|array|min:1',
            'selected_sellers.*' => 'exists:users,id',
            'prix_admin' => 'nullable|string', // Accepter les plages comme [400,500]
            'prix_vente' => 'nullable|numeric',
            'visible' => 'nullable|boolean',
            'action' => 'required|in:assign,remove',
        ]);

        $action = $data['action'];
        $selectedSellers = $data['selected_sellers'];

        // Traiter les prix admin (peut être une plage comme [400,500])
        $prixAdmin = $data['prix_admin'] ?? $product->prix_admin;
        $prixAdminForPivot = null; // Prix à utiliser dans la table pivot (decimal)

        if ($prixAdmin) {
            // Si c'est une plage [400,500], extraire les prix
            if (preg_match('/\[([^\]]+)\]/', $prixAdmin, $matches)) {
                $prixArray = array_map('floatval', explode(',', $matches[1]));
                $prixAdminForPivot = array_sum($prixArray) / count($prixArray); // Prix moyen
            } elseif (strpos($prixAdmin, ',') !== false) {
                // Si c'est une liste séparée par des virgules
                $prixArray = array_map('floatval', explode(',', $prixAdmin));
                $prixAdminForPivot = array_sum($prixArray) / count($prixArray); // Prix moyen
            } else {
                // Si c'est un seul prix
                $prixAdminForPivot = (float) $prixAdmin;
            }
        }

        $prixVente = $data['prix_vente'] ?? $product->prix_vente;

        if ($action === 'assign') {
            // Assigner les vendeurs au produit
            foreach ($selectedSellers as $sellerId) {
                $product->assignedUsers()->syncWithoutDetaching([
                    $sellerId => [
                        'prix_admin' => $prixAdminForPivot,
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
        $quantities = array_column($mergedStock, 'quantity');
        $numericQuantities = array_filter($quantities, function($q) {
            return is_numeric($q);
        });
        $totalStock = array_sum(array_map('intval', $numericQuantities));

        // Log::info('Fusion intelligente des couleurs - Debug final:', [
        //     'existing_colors_count' => count($existingColors),
        //     'new_colors_count' => count($newColors),
        //     'new_custom_colors_count' => count($newCustomColors),
        //     'merged_colors_count' => count($mergedColors),
        //     'merged_stock_count' => count($mergedStock),
        //     'total_stock_calculated' => $totalStock,
        //     'processed_colors' => $processedColors,
        //     'merged_colors' => $mergedColors,
        //     'merged_stock' => $mergedStock,
        //     'colors_removed' => $colorsToRemove ?? [],
        //     'stock_removed' => $stockToRemove ?? []
        // ]);

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

    /**
     * Synchroniser un produit avec tous les vendeurs existants
     * Assigne le produit aux vendeurs qui ne l'ont pas encore
     */
    private function syncProductWithAllSellers(Product $product)
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        $assignedSellerIds = $product->assignedUsers()->pluck('user_id')->toArray();

        $newAssignments = [];
        foreach ($sellers as $seller) {
            if (!in_array($seller->id, $assignedSellerIds)) {
                $newAssignments[$seller->id] = [
                    'prix_admin' => $product->prix_admin_moyen, // Utiliser l'accessor pour le prix moyen
                    'prix_vente' => $product->prix_vente,
                    'visible' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        if (!empty($newAssignments)) {
            $product->assignedUsers()->attach($newAssignments);
        }
    }
}
