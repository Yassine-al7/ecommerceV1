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
        $products = $query->get()->fresh();

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
        return view('admin.products.create-modern', compact('categories'));
    }

    public function store(Request $request)
    {
        // Récupérer la catégorie pour vérifier si c'est un accessoire
        $categorie = \App\Models\Category::find($request->categorie_id);
        $isAccessoire = $categorie && strtolower($categorie->name) === 'accessoire';

        // Récupérer les couleurs pour la validation dynamique
        $couleurs = $request->input('couleurs', []);
        $couleursPersonnalisees = $request->input('couleurs_personnalisees', []);

        $validationRules = [
            'name' => 'required|string|max:255',
            'couleurs' => 'required|array|min:1',
            'couleurs_hex' => 'array',
            'hidden_colors' => 'nullable|array',
            'tailles' => $isAccessoire ? 'nullable|array' : 'required|array|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Augmenté à 5MB
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|string|min:1|regex:/^[\d\s,.-]+$/', // Accepte nombres, virgules, espaces, points, tirets
            'prix_vente' => 'required|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0', // Stock global obligatoire
        ];

        // Ajouter la validation des stocks par couleur
        foreach ($couleurs as $index => $couleur) {
            $validationRules["stock_couleur_{$index}"] = 'required|integer|min:1';
        }

        foreach ($couleursPersonnalisees as $index => $couleur) {
            $validationRules["stock_couleur_custom_{$index}"] = 'required|integer|min:1';
        }

        $data = $request->validate($validationRules);

        // Traiter les couleurs avec leurs valeurs hexadécimales et stocks
        $couleursHex = $request->input('couleurs_hex', []);

        // Créer un mapping couleur-hex pour la sauvegarde
        $couleursWithHex = [];
        $stockCouleurs = [];

        // Traiter les couleurs prédéfinies
        foreach ($couleurs as $index => $couleur) {
            $hex = $couleursHex[$index] ?? '#cccccc';
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

        // Traiter les couleurs personnalisées
        foreach ($couleursPersonnalisees as $index => $couleur) {
            $stock = $request->input("stock_couleur_custom_{$index}", 0);

            $couleursWithHex[] = [
                'name' => $couleur,
                'hex' => '#cccccc' // Couleur par défaut pour les couleurs personnalisées
            ];

            $stockCouleurs[] = [
                'name' => $couleur,
                'quantity' => (int) $stock
            ];
        }

        // Convertir les couleurs en JSON (pour stockage en base)
        $data['couleur'] = $couleursWithHex; // Laravel cast automatiquement en JSON
        $data['stock_couleurs'] = $stockCouleurs; // Laravel cast automatiquement en JSON

        // Traiter les couleurs masquées
        $data['hidden_colors'] = json_encode($request->input('hidden_colors', []));

        // Calculer le stock total à partir des stocks par couleur
        $totalStock = array_sum(array_column($stockCouleurs, 'quantity'));
        $data['quantite_stock'] = $totalStock;

        // Convertir les tailles en JSON - pour les accessoires, utiliser un tableau vide
        if ($isAccessoire) {
            $data['tailles'] = []; // Laravel cast automatiquement en JSON
        } else {
            $data['tailles'] = $data['tailles'] ?? []; // Laravel cast automatiquement en JSON
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
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = '/storage/' . $imagePath;
        } else {
            // Si aucune image n'est fournie, utiliser une image par défaut
            $data['image'] = '/storage/products/default-product.svg';
        }

        // Gérer les images par couleur
        $colorImages = [];

        // Traiter les images des couleurs prédéfinies
        foreach ($couleurs as $index => $couleur) {
            $colorImageKey = "color_images_{$index}";
            if ($request->hasFile($colorImageKey)) {
                $images = [];
                foreach ($request->file($colorImageKey) as $file) {
                    $imagePath = $file->store('products/colors', 'public');
                    $images[] = '/storage/' . $imagePath;
                }
                if (!empty($images)) {
                    // Extraire le nom de la couleur (peut être un string ou un array)
                    $colorName = is_array($couleur) ? $couleur['name'] : $couleur;
                    $colorImages[] = [
                        'color' => $colorName,
                        'images' => $images
                    ];
                }
            }
        }

        // Traiter les images des couleurs personnalisées
        $customColorIndex = 0;
        foreach ($couleursPersonnalisees as $couleur) {
            $customImageKey = "custom_color_images_{$customColorIndex}";
            if ($request->hasFile($customImageKey)) {
                $images = [];
                foreach ($request->file($customImageKey) as $file) {
                    $imagePath = $file->store('products/colors', 'public');
                    $images[] = '/storage/' . $imagePath;
                }
                if (!empty($images)) {
                    $colorImages[] = [
                        'color' => $couleur,
                        'images' => $images
                    ];
                }
            }
            $customColorIndex++;
        }

        $data['color_images'] = json_encode($colorImages);

        // Si aucune image principale n'est fournie mais qu'il y a des images par couleur,
        // utiliser la première image comme image principale
        if ($data['image'] === '/storage/products/default-product.svg' && !empty($colorImages)) {
            $firstColorImages = $colorImages[0]['images'] ?? [];
            if (!empty($firstColorImages)) {
                $data['image'] = $firstColorImages[0];
            }
        }

        // Initialiser le stock par couleur si pas fourni
        if (empty($data['stock_couleurs']) && !empty($data['couleur'])) {
            $couleurs = is_array($data['couleur']) ? $data['couleur'] : [$data['couleur']];
            $stockCouleurs = [];
            foreach ($couleurs as $couleur) {
                $stockCouleurs[] = [
                    'name' => $couleur,
                    'quantity' => $data['quantite_stock'] ?? 0
                ];
            }
            $data['stock_couleurs'] = $stockCouleurs;
        }

        // Calculer le stock total basé sur les stocks par couleur
        if (!empty($data['stock_couleurs'])) {
            $stockTotal = 0;
            foreach ($data['stock_couleurs'] as $stockCouleur) {
                if (is_array($stockCouleur) && isset($stockCouleur['quantity'])) {
                    $stockTotal += (int)$stockCouleur['quantity'];
                }
            }
            $data['quantite_stock'] = $stockTotal;
        }

        $product = Product::create($data);

        // Assigner automatiquement le produit à tous les vendeurs
        $sellers = \App\Models\User::where('role', 'seller')->get();
        if ($sellers->count() > 0) {
            $pivotData = [];
            foreach ($sellers as $seller) {
                $pivotData[$seller->id] = [
                    'prix_admin' => $prixAdminMoyen, // Utiliser le prix moyen calculé
                    'prix_vente' => $product->prix_vente,
                    'visible' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            $product->assignedUsers()->attach($pivotData);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produit créé avec succès et assigné à tous les vendeurs!');
    }

    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function editModern(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('admin.products.edit-modern', compact('product', 'categories'));
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
            'hidden_colors' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Augmenté à 5MB
            'categorie_id' => 'required|exists:categories,id',
            'prix_admin' => 'required|string|min:1|regex:/^[\d\s,.-]+$/', // Accepte nombres, virgules, espaces, points, tirets
            'prix_vente' => 'required|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0', // Stock global obligatoire
        ];

        // Ajouter la validation des stocks par couleur seulement s'ils sont présents
        $couleurs = $request->input('couleurs', []);
        $couleursPersonnalisees = $request->input('couleurs_personnalisees', []);

        // Vérifier si des champs de stock par couleur sont présents
        $hasStockFields = false;
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'stock_couleur_')) {
                $hasStockFields = true;
                break;
            }
        }

        // Ajouter la validation des stocks par couleur seulement s'ils sont présents
        if ($hasStockFields) {
            foreach ($couleurs as $index => $couleur) {
                if ($request->has("stock_couleur_{$index}")) {
                    $validationRules["stock_couleur_{$index}"] = 'required|integer|min:1';
                }
            }

            foreach ($couleursPersonnalisees as $index => $couleur) {
                if ($request->has("stock_couleur_custom_{$index}")) {
                    $validationRules["stock_couleur_custom_{$index}"] = 'required|integer|min:1';
                }
            }
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

            // Traiter les couleurs personnalisées
            foreach ($couleursPersonnalisees as $index => $couleur) {
                $stock = $request->input("stock_couleur_custom_{$index}", 0);

                $couleursWithHex[] = [
                    'name' => $couleur,
                    'hex' => '#cccccc' // Couleur par défaut pour les couleurs personnalisées
                ];

                $stockCouleurs[] = [
                    'name' => $couleur,
                    'quantity' => (int) $stock
                ];
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

        // Gérer les images par couleur
        $existingColorImages = $product->color_images ?: [];
        $colorImages = $existingColorImages;

        // Traiter les images des couleurs prédéfinies
        foreach ($couleurs as $index => $couleur) {
            $colorImageKey = "color_images_{$index}";
            if ($request->hasFile($colorImageKey)) {
                $images = [];
                foreach ($request->file($colorImageKey) as $file) {
                    $imagePath = $file->store('products/colors', 'public');
                    $images[] = '/storage/' . $imagePath;
                }

                // Chercher si cette couleur existe déjà dans les images
                $colorIndex = -1;
                foreach ($colorImages as $idx => $colorImage) {
                    if (is_array($colorImage) && isset($colorImage['color']) && $colorImage['color'] === $couleur) {
                        $colorIndex = $idx;
                        break;
                    }
                }

                if ($colorIndex >= 0) {
                    // Ajouter les nouvelles images aux existantes
                    if (!isset($colorImages[$colorIndex]['images'])) {
                        $colorImages[$colorIndex]['images'] = [];
                    }
                    $colorImages[$colorIndex]['images'] = array_merge($colorImages[$colorIndex]['images'], $images);
                } else {
                    // Créer une nouvelle entrée pour cette couleur
                    $colorImages[] = [
                        'color' => $couleur,
                        'images' => $images
                    ];
                }
            }
        }

        // Traiter les images des couleurs personnalisées
        $customColorIndex = 0;
        foreach ($couleursPersonnalisees as $couleur) {
            $customImageKey = "custom_color_images_{$customColorIndex}";
            if ($request->hasFile($customImageKey)) {
                $images = [];
                foreach ($request->file($customImageKey) as $file) {
                    $imagePath = $file->store('products/colors', 'public');
                    $images[] = '/storage/' . $imagePath;
                }

                // Chercher si cette couleur existe déjà dans les images
                $colorIndex = -1;
                foreach ($colorImages as $idx => $colorImage) {
                    if (is_array($colorImage) && isset($colorImage['color']) && $colorImage['color'] === $couleur) {
                        $colorIndex = $idx;
                        break;
                    }
                }

                if ($colorIndex >= 0) {
                    // Ajouter les nouvelles images aux existantes
                    if (!isset($colorImages[$colorIndex]['images'])) {
                        $colorImages[$colorIndex]['images'] = [];
                    }
                    $colorImages[$colorIndex]['images'] = array_merge($colorImages[$colorIndex]['images'], $images);
                } else {
                    // Créer une nouvelle entrée pour cette couleur
                    $colorImages[] = [
                        'color' => $couleur,
                        'images' => $images
                    ];
                }
            }
            $customColorIndex++;
        }

        // Gérer la suppression d'images
        if ($request->has('removed_images')) {
            $removedImages = json_decode($request->input('removed_images'), true);
            foreach ($removedImages as $removedImage) {
                // Supprimer le fichier physique
                if (isset($removedImage['image'])) {
                    $imagePath = str_replace('/storage/', '', $removedImage['image']);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }

                // Supprimer de la structure des données
                foreach ($colorImages as $idx => $colorImage) {
                    if (is_array($colorImage) && isset($colorImage['color']) && $colorImage['color'] === $removedImage['color']) {
                        if (isset($colorImage['images'])) {
                            $colorImages[$idx]['images'] = array_filter($colorImage['images'], function($img) use ($removedImage) {
                                return $img !== $removedImage['image'];
                            });

                            // Si plus d'images pour cette couleur, supprimer l'entrée
                            if (empty($colorImages[$idx]['images'])) {
                                unset($colorImages[$idx]);
                            }
                        }
                    }
                }
            }
        }

        $data['color_images'] = json_encode(array_values($colorImages));

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
