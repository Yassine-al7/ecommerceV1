<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\GeneratesOrderReferences;
use App\Services\StockService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
	use GeneratesOrderReferences;

	public function index()
	{
		$allowedStatuses = ['en attente', 'confirmé', 'pas de réponse', 'expédition', 'livré', 'annulé', 'reporté', 'retourné'];

		$ordersQuery = Order::where('seller_id', auth()->id());

		if (request()->filled('status') && in_array(request('status'), $allowedStatuses, true)) {
			$ordersQuery->where('status', request('status'));
		}

		$orders = $ordersQuery->latest()->paginate(15);

		// Calculer les statistiques complètes
		$allOrders = Order::where('seller_id', auth()->id())->get();

		// Fonction de normalisation des statuts
		$normalizeStatus = function($status) {
			$status = strtolower(trim($status));
			$status = str_replace(['é', 'è', 'à', 'É', 'È', 'À'], ['e', 'e', 'a', 'e', 'e', 'a'], $status);
			return $status;
		};

		$stats = [
			'total' => $allOrders->count(),
			'en_attente' => $allOrders->filter(function($order) {
				return $order->status === 'en attente';
			})->count(),
			'confirme' => $allOrders->filter(function($order) {
				return $order->status === 'confirmé';
			})->count(),
			'expedition' => $allOrders->filter(function($order) {
				return $order->status === 'expédition';
			})->count(),
			'livre' => $allOrders->filter(function($order) {
				return $order->status === 'livré';
			})->count(),
			'problematique' => $allOrders->filter(function($order) {
				return in_array($order->status, ['annulé', 'retourné', 'reporté', 'pas de réponse']);
			})->count(),
			'pas_de_reponse' => $allOrders->filter(function($order) {
				return $order->status === 'pas de réponse';
			})->count(),
		];

		return view('seller.orders', compact('orders', 'stats'));
	}

	    public function create()
    {
        // Produits assignés au vendeur avec plus d'informations
        $products = auth()->user()->assignedProducts()
            ->select('produits.id', 'produits.name', 'produits.tailles', 'produits.image', 'produits.prix_admin', 'produits.couleur', 'produits.stock_couleurs', 'produits.hidden_colors', 'produits.categorie_id', 'produits.quantite_stock')
            ->with('category:id,name,slug')
            ->get();

                // 🆕 FILTRER LES COULEURS MASQUÉES ET AVEC STOCK = 0 POUR LES VENDEURS
        foreach ($products as $product) {
            // Utiliser les couleurs visibles (excluant les couleurs masquées)
            $visibleColors = $product->visible_colors ?? [];

            // Si pas de stock_couleurs, créer des données par défaut basées sur les couleurs visibles
            if (empty($product->stock_couleurs) && !empty($visibleColors)) {
                $stockCouleurs = [];

                foreach ($visibleColors as $couleur) {
                    $colorName = is_array($couleur) ? $couleur['name'] : $couleur;
                    $stockCouleurs[] = [
                        'name' => $colorName,
                        'quantity' => $product->quantite_stock ?? 10 // Stock par défaut
                    ];
                }

                $product->stock_couleurs = $stockCouleurs;
                \Log::info("Stock par défaut créé pour {$product->name}: " . json_encode($stockCouleurs));
            }

            // 🆕 FILTRER LES COULEURS AVEC STOCK ≤ 0 (en plus des couleurs masquées)
            if (!empty($product->stock_couleurs)) {
                // Les accesseurs du modèle ont déjà décodé les données en tableaux
                $stockCouleurs = $product->stock_couleurs;
                $visibleColors = $product->visible_colors ?? [];

                if (is_array($stockCouleurs) && is_array($visibleColors)) {
                    $couleursFiltrees = [];
                    $stockCouleursFiltres = [];

                    foreach ($stockCouleurs as $index => $stock) {
                        // Vérifier si la couleur est visible ET a du stock
                        $isVisible = false;
                        foreach ($visibleColors as $visibleColor) {
                            $visibleColorName = is_array($visibleColor) ? $visibleColor['name'] : $visibleColor;
                            if ($visibleColorName === $stock['name']) {
                                $isVisible = true;
                                break;
                            }
                        }

                        if ($isVisible && $stock['quantity'] > 0) {
                            // Conserver la couleur et son stock
                            $stockCouleursFiltres[] = $stock;

                            // Trouver la couleur correspondante dans visible_colors
                            foreach ($visibleColors as $visibleColor) {
                                $visibleColorName = is_array($visibleColor) ? $visibleColor['name'] : $visibleColor;
                                if ($visibleColorName === $stock['name']) {
                                    $couleursFiltrees[] = $visibleColor;
                                    break;
                                }
                            }
                        } else {
                            \Log::info("🗑️ Couleur filtrée pour {$product->name}: {$stock['name']} (visible: " . ($isVisible ? 'oui' : 'non') . ", stock: {$stock['quantity']})");
                        }
                    }

                    // Mettre à jour les attributs du produit pour l'affichage
                    $product->couleur = $couleursFiltrees;
                    $product->stock_couleurs = $stockCouleursFiltres;

                    \Log::info("🎨 Filtrage des couleurs pour {$product->name}:", [
                        'couleurs_visibles' => count($visibleColors),
                        'couleurs_filtrees' => count($couleursFiltrees),
                        'stock_original' => count($stockCouleurs),
                        'stock_filtre' => count($stockCouleursFiltres)
                    ]);
                }
            }

            // Si pas de couleurs après filtrage, créer une couleur par défaut
            if (empty($product->couleur)) {
                $product->couleur = ['Couleur unique'];
                $product->stock_couleurs = [
                    ['name' => 'Couleur unique', 'quantity' => $product->quantite_stock ?? 10]
                ];
                \Log::info("Couleur par défaut créée pour {$product->name}: Couleur unique");
            }

            // Debug des données finales
            \Log::info("Produit {$product->name} - Données finales:");
            \Log::info("  - Couleur: " . json_encode($product->couleur));
            \Log::info("  - Stock couleurs: " . json_encode($product->stock_couleurs));
            \Log::info("  - Tailles: " . json_encode($product->tailles));
        }

        // Debug des tailles et catégories pour vérifier ce qui est récupéré
        foreach ($products as $product) {
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Tailles dans create(): " . json_encode($product->tailles));
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Couleurs dans create(): " . json_encode($product->couleur));
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Stock couleurs dans create(): " . json_encode($product->stock_couleurs));
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Quantité stock: " . ($product->quantite_stock ?? 'Aucune'));
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Catégorie ID: " . ($product->categorie_id ?? 'Aucune'));
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Relation Category: " . json_encode($product->category ?? 'Aucune'));
        }

        return view('seller.order_form', compact('products'));
    }

	public function store(Request $request)
	{
		        $data = $request->validate([
            'nom_client' => 'required|string',
            'ville' => 'required|string',
            'adresse_client' => 'required|string',
            'numero_telephone_client' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:produits,id',
            'products.*.couleur_produit' => 'required|string',
            'products.*.taille_produit' => 'nullable|string', // Optionnel pour les accessoires
            'products.*.quantite_produit' => 'required|integer|min:1',
            'products.*.prix_vente_client' => 'required|numeric|min:0.01',
            'commentaire' => 'nullable|string',
        ]);

		// Générer une référence unique
		$data['reference'] = $this->generateUniqueOrderReference();

		// Prix de livraison selon la ville sélectionnée (normalisation clé/nom)
		$prixLivraison = 0;
		if (!empty($data['ville'])) {
			$rawVille = (string) $data['ville'];
			$normKey = (string) \Illuminate\Support\Str::of($rawVille)->lower()->ascii()->replaceMatches('/\s+/', '_');

			// 1) Essayer par clé normalisée
			$cityConfig = config("delivery.cities.{$normKey}");
			// 2) Sinon, chercher par nom normalisé
			if (!$cityConfig) {
				$cities = config('delivery.cities', []);
				foreach ($cities as $cfg) {
					$nameNorm = (string) \Illuminate\Support\Str::of($cfg['name'] ?? '')->lower()->ascii()->replaceMatches('/\s+/', '_');
					if ($nameNorm === $normKey) { $cityConfig = $cfg; break; }
				}
			}

			if ($cityConfig && isset($cityConfig['price'])) {
				$prixLivraison = (float) $cityConfig['price'];
			}
		}

		// Traiter chaque produit selon la logique de l'utilisateur
		$produits = [];
		$prixTotalCommande = 0;
		$margeTotaleProduits = 0;

		foreach ($data['products'] as $productData) {
			$product = auth()->user()->assignedProducts()->where('produits.id', $productData['product_id'])->with('category')->firstOrFail();

			            // Récupérer les tailles et couleurs spécifiques de ce produit depuis la base de données
            // Les accesseurs du modèle ont déjà décodé les données en tableaux
            $tailles = $product->tailles;
            $couleurs = $product->couleur;
            $stockCouleurs = $product->stock_couleurs;

            // Debug des tailles et couleurs
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Tailles: " . json_encode($tailles));
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Couleurs: " . json_encode($couleurs));
            \Log::info("Produit {$product->name} (ID: {$product->id}) - Stock couleurs: " . json_encode($stockCouleurs));

            // Vérifier si c'est un accessoire par catégorie (plus précis)
            $isAccessoire = $product->category && $product->category->slug === 'accessoires';
            \Log::info("Produit {$product->name} - Catégorie: " . ($product->category ? $product->category->name : 'Aucune'));
            \Log::info("Produit {$product->name} - Est accessoire par catégorie: " . ($isAccessoire ? 'OUI' : 'NON'));

            // Fallback : si pas de catégorie, utiliser la détection par tailles
            if (!$product->category) {
                $isAccessoire = empty($tailles);
                \Log::info("Produit {$product->name} - Fallback: Est accessoire par tailles: " . ($isAccessoire ? 'OUI' : 'NON'));
            }

            // Si aucune taille n'est définie et que ce n'est pas un accessoire, utiliser des tailles par défaut
            if (empty($tailles) && !$isAccessoire) {
                $tailles = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                \Log::info("Produit {$product->name} - Utilisation des tailles par défaut: " . json_encode($tailles));
            }

			// Vérifier la disponibilité de la couleur sélectionnée en respectant l'état masqué/démasqué
			$couleurSelectionnee = $productData['couleur_produit'];
			$couleurDisponible = false;
			$stockCouleur = 0;

			// Normaliser une couleur (pour comparer sans casse/espaces)
			$normalizeColor = function ($value) {
				$name = is_array($value) ? ($value['name'] ?? '') : (string) $value;
				return strtolower(trim($name));
			};

			$normSelected = $normalizeColor($couleurSelectionnee);

			// Listes utiles
			$visibleColors = $product->visible_colors ?? [];
			$hiddenColors = $product->hidden_colors ?? [];

			// 0) Si explicitement masquée, refuser
			$isHidden = false;
			if (is_array($hiddenColors)) {
				foreach ($hiddenColors as $hidden) {
					if ($normalizeColor($hidden) === $normSelected) {
						$isHidden = true;
						break;
					}
				}
			}
			if ($isHidden) {
				return back()->withErrors(['couleur_produit' => "La couleur '{$couleurSelectionnee}' est masquée pour le produit '{$product->name}'"])->withInput();
			}

			// 1) Autoriser toute couleur présente dans visible_colors (démasquée), même si absente de stock_couleurs
			if (is_array($visibleColors)) {
				foreach ($visibleColors as $visibleColor) {
					if ($normalizeColor($visibleColor) === $normSelected) {
						$couleurDisponible = true;
						break;
					}
				}
			}

			// 2) Si une entrée existe dans stock_couleurs, récupérer le stock réel et considérer comme existante
			if (is_array($stockCouleurs)) {
				foreach ($stockCouleurs as $stockColor) {
					if (is_array($stockColor) && isset($stockColor['name']) && $normalizeColor($stockColor['name']) === $normSelected) {
						$stockCouleur = (int) ($stockColor['quantity'] ?? 0);
						$couleurDisponible = true; // Couleur connue via stock
						\Log::info("Stock détecté pour {$couleurSelectionnee}: {$stockCouleur}");
						break;
					}
				}
			}

			// 3) Si la couleur n'est trouvée nulle part (et pas masquée), la refuser
			if (!$couleurDisponible) {
				return back()->withErrors(['couleur_produit' => "La couleur '{$couleurSelectionnee}' n'existe pas pour le produit '{$product->name}'"])->withInput();
			}

			// La vérification de taille reste gérée plus bas par le bloc existant

			// Avertissement si stock insuffisant (mais permet la commande)
			if ($stockCouleur < (int) $productData['quantite_produit']) {
				\Log::warning("Commande en rupture de stock: {$productData['quantite_produit']} {$couleurSelectionnee} demandés, seulement {$stockCouleur} disponibles");
				// Note: On permet la commande même en rupture de stock
			}

			// Note: La validation de quantité est maintenant gérée dans la section d'avertissement ci-dessus
			// Les commandes en rupture de stock sont autorisées

                // Validation des tailles (seulement si ce n'est pas un accessoire)
                if (!$isAccessoire) {
                    // Nettoyer la taille sélectionnée (supprimer les caractères de formatage éventuels)
                    $tailleSelectionnee = preg_replace('/[\[\]\'"]/', '', trim((string)$productData['taille_produit']));

                    // Vérifier que la taille est fournie pour les produits non-accessoires
                    if (empty($tailleSelectionnee)) {
                        \Log::warning("Taille manquante pour le produit {$product->name} (non-accessoire)");
                        return back()->withErrors(['taille_produit' => "La taille est obligatoire pour le produit '{$product->name}'"])->withInput();
                    }

                    // Nettoyer aussi les tailles disponibles
                    // S'assurer que $tailles est un tableau avant d'utiliser array_map
                    if (!is_array($tailles)) {
                        \Log::warning("Tailles n'est pas un tableau pour {$product->name}, conversion en tableau: " . json_encode($tailles));
                        $tailles = is_string($tailles) ? json_decode($tailles, true) : [];
                        if (!is_array($tailles)) {
                            $tailles = ['S', 'M', 'L']; // Fallback par défaut
                        }
                    }

                    $taillesClean = array_map(function($taille) {
                        return preg_replace('/[\[\]\'"]/', '', trim((string)$taille));
                    }, $tailles);

                    \Log::info("Taille sélectionnée nettoyée: '{$tailleSelectionnee}' pour le produit {$product->name}");
                    \Log::info("Tailles disponibles nettoyées: " . json_encode($taillesClean));

                    // Vérifier que la taille sélectionnée est disponible (après nettoyage)
                    if (!in_array($tailleSelectionnee, $taillesClean)) {
                        \Log::warning("Taille '{$tailleSelectionnee}' non disponible pour le produit {$product->name}. Tailles disponibles: " . json_encode($taillesClean));
                        return back()->withErrors(['taille_produit' => "La taille '{$tailleSelectionnee}' n'est pas disponible pour le produit '{$product->name}'. Tailles disponibles: " . implode(', ', $taillesClean)])->withInput();
                    }
                } else {
                    // Pour les accessoires, pas de validation de taille
                    $tailleSelectionnee = 'N/A';
                    \Log::info("Produit {$product->name} est un accessoire - Pas de validation de taille");
                }

			$prixVenteVendeur = (float) optional($product->pivot)->prix_vente;
			$prixVenteClient = (float) $productData['prix_vente_client'];

			// Vérifier que le prix de vente au client est suffisant
			if ($prixVenteClient <= $prixVenteVendeur) {
				return back()->withErrors(['prix_vente_client' => 'Le prix de vente doit être supérieur au prix d\'achat pour avoir une marge bénéfice'])->withInput();
			}

			// 🎯 LOGIQUE MÉTIER CORRIGÉE : Calcul de la marge selon la demande de l'utilisateur
			// Prix de vente client = Prix fixe (pas × quantité)
			// Prix d'achat vendeur = Prix d'achat × quantité
			// Marge brute = Prix de vente - Prix d'achat total

			$prixAchatTotal = $prixVenteVendeur * (int) $productData['quantite_produit'];
			$margeBrute = $prixVenteClient - $prixAchatTotal;

			// Prix total de la commande = Prix de vente fixe (pas × quantité)
			$prixProduit = $prixVenteClient;

			$prixTotalCommande += $prixProduit;
			$margeTotaleProduits += $margeBrute;

			// Ajouter le produit à la liste avec tous les détails
			                $produits[] = [
                    'product_id' => $productData['product_id'],
                    'qty' => (int) $productData['quantite_produit'],
                    'couleur' => $couleurSelectionnee,
                    'taille' => $tailleSelectionnee, // Utiliser la taille nettoyée
                    'prix_vente_client' => $prixVenteClient,
                    'prix_achat_vendeur' => $prixVenteVendeur,
                    'prix_achat_total' => $prixAchatTotal,
                    'marge_brute' => $margeBrute
                ];
		}

		// Calcul de la marge bénéfice finale selon la logique de l'utilisateur
		// Marge finale = Marge totale pièces - Prix de livraison
		$margeBenefice = $margeTotaleProduits - $prixLivraison;

		// Préparer les données pour la création de la commande
		$orderData = [
			'reference' => $data['reference'],
			'nom_client' => $data['nom_client'],
			'ville' => $data['ville'],
			'adresse_client' => $data['adresse_client'],
			'numero_telephone_client' => $data['numero_telephone_client'],
			'produits' => json_encode($produits),
			'prix_commande' => $prixTotalCommande,
			'marge_benefice' => $margeBenefice,
			'status' => 'en attente',
			'seller_id' => auth()->id(),
			'commentaire' => $data['commentaire'] ?? null,
		];

		$order = Order::create($orderData);

		// Diminuer automatiquement le stock pour chaque produit
		foreach ($produits as $productData) {
			$success = StockService::decreaseStock(
				$productData['product_id'],
				$productData['couleur'],
				$productData['qty']
			);

			if (!$success) {
				Log::error("Échec de la mise à jour du stock pour le produit ID: {$productData['product_id']}");
			}
		}

		return redirect()->route('seller.orders.index')->with('success', "Commande créée avec succès ! Référence: {$order->reference}, Prix total: " . number_format($prixTotalCommande, 2) . " DH (prix de vente fixe, pas × quantité), Marge produits: " . number_format($margeTotaleProduits, 2) . " DH, Marge finale: " . number_format($margeBenefice, 2) . " DH");
	}

	public function show($id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
		return view('seller.order_detail', compact('order'));
	}

	public function edit($id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

		// Produits assignés au vendeur avec plus d'informations
		$products = auth()->user()->assignedProducts()
			->select('produits.id', 'produits.name', 'produits.tailles', 'produits.image', 'produits.prix_admin', 'produits.couleur', 'produits.stock_couleurs', 'produits.hidden_colors', 'produits.categorie_id')
			->with('category:id,name,slug')
			->get();

		// Décoder les produits de la commande existante pour restaurer le stock temporairement
		$orderProducts = json_decode($order->produits, true) ?: [];

		// Créer un mapping des quantités de la commande par produit/couleur
		$orderQuantities = [];
		foreach ($orderProducts as $orderProduct) {
			$key = $orderProduct['product_id'] . '_' . ($orderProduct['couleur'] ?? 'default');
			$orderQuantities[$key] = (int) ($orderProduct['qty'] ?? 0);
		}

		// Traiter les produits pour l'édition : afficher toutes les couleurs visibles + restaurer le stock temporairement
		foreach ($products as $product) {
			// Utiliser les couleurs visibles (excluant les couleurs masquées)
			$visibleColors = $product->visible_colors ?? [];

			// Pour l'édition, on affiche toutes les couleurs visibles, même avec stock 0
			if (!empty($visibleColors)) {
				// Mettre à jour les couleurs du produit avec les couleurs visibles
				$product->couleur = $visibleColors;

				// S'assurer que stock_couleurs contient toutes les couleurs visibles
				$stockCouleurs = $product->stock_couleurs ?? [];
				$stockCouleursArray = is_array($stockCouleurs) ? $stockCouleurs : [];

				// Créer un mapping des stocks existants
				$existingStocks = [];
				foreach ($stockCouleursArray as $stock) {
					if (is_array($stock) && isset($stock['name'])) {
						$existingStocks[$stock['name']] = (int) ($stock['quantity'] ?? 0);
					}
				}

								// Créer le stock_couleurs final avec toutes les couleurs visibles
				$finalStockCouleurs = [];
				foreach ($visibleColors as $couleur) {
					$colorName = is_array($couleur) ? $couleur['name'] : $couleur;
					$currentStock = $existingStocks[$colorName] ?? 0;

					// 🆕 LOGIQUE INTELLIGENTE DE RESTAURATION DU STOCK
					$key = $product->id . '_' . $colorName;
					$orderQuantity = $orderQuantities[$key] ?? 0;

					$displayStock = $currentStock;

					if ($orderQuantity > 0) {
						// Cas 1: Stock actuel < quantité commandée → Erreur (ne pas restaurer)
						if ($currentStock < $orderQuantity) {
							\Log::warning("Édition - Produit {$product->name}, Couleur {$colorName}: ERREUR - Stock actuel ({$currentStock}) < quantité commandée ({$orderQuantity})");
							$displayStock = $currentStock; // Garder le stock réel pour montrer l'erreur
						}
						// Cas 2: Stock actuel = quantité commandée → Garder le stock actuel (10)
						elseif ($currentStock == $orderQuantity) {
							\Log::info("Édition - Produit {$product->name}, Couleur {$colorName}: Stock égal à la commande ({$currentStock}), pas de restauration");
							$displayStock = $currentStock; // Garder le stock actuel
						}
						// Cas 3: Stock actuel > quantité commandée → Restaurer temporairement
						else {
							$displayStock = $currentStock + $orderQuantity;
							\Log::info("Édition - Produit {$product->name}, Couleur {$colorName}: Stock restauré de {$currentStock} à {$displayStock} (+{$orderQuantity})");
						}
					}

					$finalStockCouleurs[] = [
						'name' => $colorName,
						'quantity' => $displayStock
					];
				}

				$product->stock_couleurs = $finalStockCouleurs;
				\Log::info("Édition - Produit {$product->name}: Couleurs visibles affichées avec stock temporairement restauré");
			} else {
				// Si pas de couleurs visibles, créer une couleur par défaut
				$product->couleur = ['Couleur unique'];
				$product->stock_couleurs = [
					['name' => 'Couleur unique', 'quantity' => $product->quantite_stock ?? 10]
				];
			}
		}

		return view('seller.order_form', compact('order', 'products', 'orderProducts'));
	}

	public function update(Request $request, $id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

		// Vérifier que la commande peut être modifiée (pas encore livrée ou dans un statut problématique)
		if (in_array($order->status, ['livré', 'annulé', 'reporté', 'retourné'])) {
			return back()->withErrors(['status' => 'Cette commande ne peut plus être modifiée.']);
		}

		$data = $request->validate([
			'nom_client' => 'required|string',
			'ville' => 'required|string',
			'adresse_client' => 'required|string',
			'numero_telephone_client' => 'required|string',
			'products' => 'required|array|min:1',
			'products.*.product_id' => 'required|exists:produits,id',
			'products.*.couleur_produit' => 'required|string',
			'products.*.taille_produit' => 'nullable|string', // Optionnel pour les accessoires
			'products.*.quantite_produit' => 'required|integer|min:1',
			'products.*.prix_vente_client' => 'required|numeric|min:0.01',
			'commentaire' => 'nullable|string',
		]);

		// Prix de livraison selon la ville sélectionnée
		$prixLivraison = 0;
		if (isset($data['ville']) && $data['ville'] !== '') {
			$cityConfig = config("delivery.cities.{$data['ville']}");
			if ($cityConfig) {
				$prixLivraison = $cityConfig['price'];
			}
		}

		// Traiter chaque produit selon la logique de l'utilisateur
		$produits = [];
		$prixTotalCommande = 0;
		$margeTotaleProduits = 0;

		foreach ($data['products'] as $productData) {
			$product = auth()->user()->assignedProducts()->where('produits.id', $productData['product_id'])->with('category')->firstOrFail();

			// Récupérer les tailles spécifiques de ce produit depuis la base de données
			// Les accesseurs du modèle ont déjà décodé les données en tableaux
			$tailles = $product->tailles;

			// Vérifier si c'est un accessoire par catégorie (plus précis)
			$isAccessoire = $product->category && $product->category->slug === 'accessoires';
			\Log::info("Produit {$product->name} - Catégorie: " . ($product->category ? $product->category->name : 'Aucune'));
			\Log::info("Produit {$product->name} - Est accessoire par catégorie: " . ($isAccessoire ? 'OUI' : 'NON'));

			// Fallback : si pas de catégorie, utiliser la détection par tailles
			if (!$product->category) {
				$isAccessoire = empty($tailles);
				\Log::info("Produit {$product->name} - Fallback: Est accessoire par tailles: " . ($isAccessoire ? 'OUI' : 'NON'));
			}

			// Gérer la taille selon le type de produit
			if (!$isAccessoire) {
				// Si aucune taille n'est définie mais que ce n'est pas un accessoire, utiliser des tailles par défaut
				if (empty($tailles)) {
					$tailles = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
				}

				// Nettoyer la taille sélectionnée
				$tailleSelectionnee = preg_replace('/[\[\]\'"]/', '', trim((string)$productData['taille_produit']));

				// Vérifier que la taille est fournie pour les produits non-accessoires
				if (empty($tailleSelectionnee)) {
					return back()->withErrors(['taille_produit' => "La taille est obligatoire pour le produit '{$product->name}'"])->withInput();
				}

				// Nettoyer aussi les tailles disponibles
				// S'assurer que $tailles est un tableau avant d'utiliser array_map
				if (!is_array($tailles)) {
					\Log::warning("Tailles n'est pas un tableau pour {$product->name}, conversion en tableau: " . json_encode($tailles));
					$tailles = is_string($tailles) ? json_decode($tailles, true) : [];
					if (!is_array($tailles)) {
						$tailles = ['S', 'M', 'L']; // Fallback par défaut
					}
				}

				$taillesClean = array_map(function($taille) {
					return preg_replace('/[\[\]\'"]/', '', trim((string)$taille));
				}, $tailles);

				// Vérifier que la taille sélectionnée est disponible
				if (!in_array($tailleSelectionnee, $taillesClean)) {
					return back()->withErrors(['taille_produit' => "La taille '{$tailleSelectionnee}' n'est pas disponible pour le produit '{$product->name}'. Tailles disponibles: " . implode(', ', $taillesClean)])->withInput();
				}
			} else {
				// Pour les accessoires, pas de validation de taille
				$tailleSelectionnee = 'N/A';
				\Log::info("Produit {$product->name} est un accessoire - Pas de validation de taille");
			}

			$prixVenteVendeur = (float) optional($product->pivot)->prix_vente;
			$prixVenteClient = (float) $productData['prix_vente_client'];

			if ($prixVenteClient <= $prixVenteVendeur) {
				return back()->withErrors(['prix_vente_client' => 'Le prix de vente doit être supérieur au prix d\'achat pour avoir une marge bénéfice'])->withInput();
			}

			$margeParPiece = $prixVenteClient - $prixVenteVendeur;
			$margeTotalePieces = $margeParPiece * (int) $productData['quantite_produit'];
			$prixProduit = $prixVenteClient; // Prix fixe, pas multiplié par la quantité

			$prixTotalCommande += $prixProduit;
			$margeTotaleProduits += $margeTotalePieces;

			$produits[] = [
				'product_id' => $productData['product_id'],
				'qty' => (int) $productData['quantite_produit'],
				'couleur' => $productData['couleur_produit'],
				'taille' => $tailleSelectionnee,
				'prix_vente_client' => $prixVenteClient,
				'prix_achat_vendeur' => $prixVenteVendeur,
				'marge_par_piece' => $margeParPiece,
				'marge_produit' => $margeTotalePieces
			];
		}

		$margeBenefice = $margeTotaleProduits - $prixLivraison;

		// 🆕 GESTION DU STOCK POUR L'ÉDITION : Comparer anciennes vs nouvelles quantités
		$oldProducts = json_decode($order->produits, true) ?: [];

		// Créer un mapping des anciennes quantités par produit/couleur
		$oldQuantities = [];
		foreach ($oldProducts as $oldProduct) {
			$key = $oldProduct['product_id'] . '_' . ($oldProduct['couleur'] ?? 'default');
			$oldQuantities[$key] = (int) ($oldProduct['qty'] ?? 0);
		}

		// Ajuster le stock selon les différences
		foreach ($produits as $newProduct) {
			$productId = $newProduct['product_id'];
			$couleur = $newProduct['couleur'] ?? 'default';
			$newQty = (int) $newProduct['qty'];
			$key = $productId . '_' . $couleur;

			$oldQty = $oldQuantities[$key] ?? 0;
			$difference = $newQty - $oldQty;

			if ($difference != 0) {
				// Ajuster le stock selon la différence
				if ($difference > 0) {
					// Quantité augmentée : diminuer le stock
					$success = StockService::decreaseStock($productId, $couleur, $difference);
					Log::info("Stock diminué pour édition: Produit {$productId}, Couleur {$couleur}, Différence: -{$difference}");
				} else {
					// Quantité diminuée : augmenter le stock
					$success = StockService::increaseStock($productId, $couleur, abs($difference));
					Log::info("Stock augmenté pour édition: Produit {$productId}, Couleur {$couleur}, Différence: +" . abs($difference));
				}

				if (!$success) {
					Log::error("Échec de l'ajustement du stock pour l'édition - Produit ID: {$productId}, Couleur: {$couleur}");
				}
			} else {
				Log::info("Aucun ajustement de stock nécessaire pour Produit {$productId}, Couleur {$couleur} (quantité inchangée)");
			}
		}

		// Mettre à jour la commande
		$order->update([
			'nom_client' => $data['nom_client'],
			'ville' => $data['ville'],
			'adresse_client' => $data['adresse_client'],
			'numero_telephone_client' => $data['numero_telephone_client'],
			'produits' => json_encode($produits),
			'prix_commande' => $prixTotalCommande,
			'marge_benefice' => $margeBenefice,
			'commentaire' => $data['commentaire'] ?? null,
		]);

		return redirect()->route('seller.orders.index')->with('success', "Commande modifiée avec succès ! Référence: {$order->reference}, Prix total: " . number_format($prixTotalCommande, 2) . " DH, Marge finale: " . number_format($margeBenefice, 2) . " DH");
	}

	public function updateStatus(Request $request, $id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
		$order->status = $request->input('status');
		$order->save();

		return redirect()->route('seller.orders.index')->with('success', 'Order status updated successfully.');
	}


}
