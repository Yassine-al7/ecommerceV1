<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\GeneratesOrderReferences;
use App\Services\StockService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
	use GeneratesOrderReferences;

	public function index()
	{
		$allowedStatuses = ['en attente', 'en cours', 'livré', 'annulé', 'confirme', 'en livraison', 'retourné', 'pas de réponse'];

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
			'en_attente' => $allOrders->filter(function($order) use ($normalizeStatus) {
				return in_array($normalizeStatus($order->status), ['en attente', 'en attente']);
			})->count(),
			'en_cours' => $allOrders->filter(function($order) use ($normalizeStatus) {
				return in_array($normalizeStatus($order->status), ['en cours', 'en cours']);
			})->count(),
			'livre' => $allOrders->filter(function($order) use ($normalizeStatus) {
				return in_array($normalizeStatus($order->status), ['livre', 'livre']);
			})->count(),
			'annule' => $allOrders->filter(function($order) use ($normalizeStatus) {
				return in_array($normalizeStatus($order->status), ['annule', 'annule']);
			})->count(),
			'confirme' => $allOrders->filter(function($order) use ($normalizeStatus) {
				return in_array($normalizeStatus($order->status), ['confirme', 'confirme']);
			})->count(),
			'en_livraison' => $allOrders->filter(function($order) use ($normalizeStatus) {
				return in_array($normalizeStatus($order->status), ['en livraison', 'en livraison']);
			})->count(),
			'problematique' => $allOrders->filter(function($order) use ($normalizeStatus) {
				return in_array($normalizeStatus($order->status), ['annule', 'annule', 'retourne', 'retourne']);
			})->count(),
			'pas_de_reponse' => $allOrders->filter(function($order) use ($normalizeStatus) {
				return in_array($normalizeStatus($order->status), ['pas de reponse', 'pas de reponse']);
			})->count(),
		];

		return view('seller.orders', compact('orders', 'stats'));
	}

	    public function create()
    {
        // Produits assignés au vendeur avec plus d'informations
        $products = auth()->user()->assignedProducts()
            ->select('produits.id', 'produits.name', 'produits.tailles', 'produits.image', 'produits.prix_admin', 'produits.couleur', 'produits.stock_couleurs', 'produits.categorie_id', 'produits.quantite_stock')
            ->with('category:id,name,slug')
            ->get();

                // Traiter les produits pour s'assurer qu'ils ont des données de stock
        foreach ($products as $product) {
            // Si pas de stock_couleurs, créer des données par défaut basées sur les couleurs
            if (empty($product->stock_couleurs) && !empty($product->couleur)) {
                $couleurs = json_decode($product->couleur, true) ?: [];
                $stockCouleurs = [];

                foreach ($couleurs as $couleur) {
                    $colorName = is_array($couleur) ? $couleur['name'] : $couleur;
                    $stockCouleurs[] = [
                        'name' => $colorName,
                        'quantity' => $product->quantite_stock ?? 10 // Stock par défaut
                    ];
                }

                $product->stock_couleurs = json_encode($stockCouleurs);
                \Log::info("Stock par défaut créé pour {$product->name}: " . json_encode($stockCouleurs));
            }

            // Si stock_couleurs existe mais contient des quantités de 0, les initialiser avec le stock total
            if (!empty($product->stock_couleurs) && $product->quantite_stock > 0) {
                $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];
                $hasZeroStock = false;

                foreach ($stockCouleurs as $stockColor) {
                    if (isset($stockColor['quantity']) && $stockColor['quantity'] === 0) {
                        $hasZeroStock = true;
                        break;
                    }
                }

                if ($hasZeroStock) {
                    // Répartir équitablement le stock total entre les couleurs
                    $nombreCouleurs = count($stockCouleurs);
                    $stockParCouleur = (int) ($product->quantite_stock / $nombreCouleurs);
                    $reste = $product->quantite_stock % $nombreCouleurs;

                    foreach ($stockCouleurs as $index => &$stockColor) {
                        $stockColor['quantity'] = $stockParCouleur;
                        if ($index === 0) {
                            $stockColor['quantity'] += $reste;
                        }
                    }

                    $product->stock_couleurs = json_encode($stockCouleurs);
                    \Log::info("Stock par couleur initialisé pour {$product->name}: " . json_encode($stockCouleurs));
                }
            }

            // Si pas de couleurs, créer une couleur par défaut
            if (empty($product->couleur)) {
                $product->couleur = json_encode(['Couleur unique']);
                $product->stock_couleurs = json_encode([
                    ['name' => 'Couleur unique', 'quantity' => $product->quantite_stock ?? 10]
                ]);
                \Log::info("Couleur par défaut créée pour {$product->name}: Couleur unique");
            }

            // Debug des données finales
            \Log::info("Produit {$product->name} - Données finales:");
            \Log::info("  - Couleur: " . $product->couleur);
            \Log::info("  - Stock couleurs: " . $product->stock_couleurs);
            \Log::info("  - Tailles: " . $product->tailles);
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

			            // Récupérer les tailles et couleurs spécifiques de ce produit depuis la base de données
            $tailles = json_decode($product->tailles, true) ?: [];
            $couleurs = json_decode($product->couleur, true) ?: [];
            $stockCouleurs = json_decode($product->stock_couleurs, true) ?: [];

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

			// Vérifier la disponibilité de la couleur sélectionnée
			$couleurSelectionnee = $productData['couleur_produit'];
			$couleurDisponible = false;
			$stockCouleur = 0;

			foreach ($stockCouleurs as $stockColor) {
				if (is_array($stockColor) && isset($stockColor['name']) && $stockColor['name'] === $couleurSelectionnee) {
					$stockCouleur = (int) ($stockColor['quantity'] ?? 0);

					// Utiliser le stock réel de la couleur (pas de fallback automatique)
					\Log::info("Stock utilisé pour {$couleurSelectionnee}: {$stockCouleur} (stock réel de la couleur)");

					// La couleur est disponible même avec stock 0 (permet la commande en rupture)
					$couleurDisponible = true;
					break;
				}
			}

			// Vérifier que la couleur existe (pas de vérification de stock > 0)
			if (!$couleurDisponible) {
				return back()->withErrors(['couleur_produit' => "La couleur '{$couleurSelectionnee}' n'existe pas pour le produit '{$product->name}'"])->withInput();
			}

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
			->select('produits.id', 'produits.name', 'produits.tailles', 'produits.image', 'produits.prix_admin', 'produits.couleur', 'produits.stock_couleurs', 'produits.categorie_id')
			->with('category:id,name,slug')
			->get();

		// Traiter les produits pour s'assurer qu'ils ont des données de stock
		foreach ($products as $product) {
			// Si pas de stock_couleurs, créer des données par défaut basées sur les couleurs
			if (empty($product->stock_couleurs) && !empty($product->couleur)) {
				$couleurs = json_decode($product->couleur, true) ?: [];
				$stockCouleurs = [];

				foreach ($couleurs as $couleur) {
					$colorName = is_array($couleur) ? $couleur['name'] : $couleur;
					$stockCouleurs[] = [
						'name' => $colorName,
						'quantity' => $product->quantite_stock ?? 10 // Stock par défaut
					];
				}

				$product->stock_couleurs = json_encode($stockCouleurs);
			}

			// Si pas de couleurs, créer une couleur par défaut
			if (empty($product->couleur)) {
				$product->couleur = json_encode(['Couleur unique']);
				$product->stock_couleurs = json_encode([
					['name' => 'Couleur unique', 'quantity' => $product->quantite_stock ?? 10]
				]);
			}
		}

		// Décoder les produits de la commande existante
		$orderProducts = json_decode($order->produits, true) ?: [];

		return view('seller.order_form', compact('order', 'products', 'orderProducts'));
	}

	public function update(Request $request, $id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

		// Vérifier que la commande peut être modifiée (pas encore livrée)
		if (in_array($order->status, ['livré', 'annulé'])) {
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
			$tailles = json_decode($product->tailles, true) ?: [];

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
				'taille' => $tailleSelectionnee,
				'prix_vente_client' => $prixVenteClient,
				'prix_achat_vendeur' => $prixVenteVendeur,
				'marge_par_piece' => $margeParPiece,
				'marge_produit' => $margeTotalePieces
			];
		}

		$margeBenefice = $margeTotaleProduits - $prixLivraison;

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
