<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\GeneratesOrderReferences;
use Illuminate\Http\Request;

class OrderController extends Controller
{
	use GeneratesOrderReferences;

	public function index()
	{
		$allowedStatuses = ['en attente', 'en cours', 'livré', 'annulé'];

		$ordersQuery = Order::where('seller_id', auth()->id());

		if (request()->filled('status') && in_array(request('status'), $allowedStatuses, true)) {
			$ordersQuery->where('status', request('status'));
		}

		$orders = $ordersQuery->latest()->paginate(15);
		return view('seller.orders', compact('orders'));
	}

	public function create()
	{
		// Produits assignés au vendeur avec plus d'informations
		$products = auth()->user()->assignedProducts()
			->select('produits.id', 'produits.name', 'produits.tailles', 'produits.image', 'produits.prix_admin')
			->get();

		// Debug des tailles pour vérifier ce qui est récupéré
		foreach ($products as $product) {
			\Log::info("Produit {$product->name} (ID: {$product->id}) - Tailles dans create(): " . json_encode($product->tailles));
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
			'products.*.taille_produit' => 'required|string',
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
			$product = auth()->user()->assignedProducts()->where('produits.id', $productData['product_id'])->firstOrFail();

			// Récupérer les tailles spécifiques de ce produit depuis la base de données
			$tailles = json_decode($product->tailles, true) ?: [];

			// Debug des tailles
			\Log::info("Produit {$product->name} (ID: {$product->id}) - Tailles: " . json_encode($tailles));

			// Si aucune taille n'est définie, utiliser des tailles par défaut
			if (empty($tailles)) {
				$tailles = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
				\Log::info("Produit {$product->name} - Utilisation des tailles par défaut: " . json_encode($tailles));
			}

			                // Nettoyer la taille sélectionnée (supprimer les caractères de formatage éventuels)
                $tailleSelectionnee = preg_replace('/[\[\]\'"]/', '', trim($productData['taille_produit']));

                // Nettoyer aussi les tailles disponibles
                $taillesClean = array_map(function($taille) {
                    return preg_replace('/[\[\]\'"]/', '', trim($taille));
                }, $tailles);

                \Log::info("Taille sélectionnée nettoyée: '{$tailleSelectionnee}' pour le produit {$product->name}");
                \Log::info("Tailles disponibles nettoyées: " . json_encode($taillesClean));

                // Vérifier que la taille sélectionnée est disponible (après nettoyage)
                if (!in_array($tailleSelectionnee, $taillesClean)) {
                    \Log::warning("Taille '{$tailleSelectionnee}' non disponible pour le produit {$product->name}. Tailles disponibles: " . json_encode($taillesClean));
                    return back()->withErrors(['taille_produit' => "La taille '{$tailleSelectionnee}' n'est pas disponible pour le produit '{$product->name}'. Tailles disponibles: " . implode(', ', $taillesClean)])->withInput();
                }

			$prixVenteVendeur = (float) optional($product->pivot)->prix_vente;
			$prixVenteClient = (float) $productData['prix_vente_client'];

			// Vérifier que le prix de vente au client est suffisant
			if ($prixVenteClient <= $prixVenteVendeur) {
				return back()->withErrors(['prix_vente_client' => 'Le prix de vente doit être supérieur au prix d\'achat pour avoir une marge bénéfice'])->withInput();
			}

			// Calcul de la marge selon la logique de l'utilisateur
			// Marge par pièce = Prix de vente - Prix d'achat
			$margeParPiece = $prixVenteClient - $prixVenteVendeur;

			// Marge totale sur toutes les pièces de ce produit
			$margeTotalePieces = $margeParPiece * (int) $productData['quantite_produit'];

			// Prix total pour ce produit
			$prixProduit = $prixVenteClient * (int) $productData['quantite_produit'];

			$prixTotalCommande += $prixProduit;
			$margeTotaleProduits += $margeTotalePieces;

			// Ajouter le produit à la liste avec tous les détails
			                $produits[] = [
                    'product_id' => $productData['product_id'],
                    'qty' => (int) $productData['quantite_produit'],
                    'taille' => $tailleSelectionnee, // Utiliser la taille nettoyée
                    'prix_vente_client' => $prixVenteClient,
                    'prix_achat_vendeur' => $prixVenteVendeur,
                    'marge_par_piece' => $margeParPiece,
                    'marge_produit' => $margeTotalePieces
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

		return redirect()->route('seller.orders.index')->with('success', "Commande créée avec succès ! Référence: {$order->reference}, Prix total: " . number_format($prixTotalCommande, 2) . " DH, Marge produits: " . number_format($margeTotaleProduits, 2) . " DH, Marge finale: " . number_format($margeBenefice, 2) . " DH");
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
			->select('produits.id', 'produits.name', 'produits.tailles', 'produits.image', 'produits.prix_admin')
			->get();

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
			'products.*.taille_produit' => 'required|string',
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
			$product = auth()->user()->assignedProducts()->where('produits.id', $productData['product_id'])->firstOrFail();

			// Récupérer les tailles spécifiques de ce produit depuis la base de données
			$tailles = json_decode($product->tailles, true) ?: [];

			// Si aucune taille n'est définie, utiliser des tailles par défaut
			if (empty($tailles)) {
				$tailles = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
			}

			// Nettoyer la taille sélectionnée
			$tailleSelectionnee = preg_replace('/[\[\]\'"]/', '', trim($productData['taille_produit']));

			// Nettoyer aussi les tailles disponibles
			$taillesClean = array_map(function($taille) {
				return preg_replace('/[\[\]\'"]/', '', trim($taille));
			}, $tailles);

			// Vérifier que la taille sélectionnée est disponible
			if (!in_array($tailleSelectionnee, $taillesClean)) {
				return back()->withErrors(['taille_produit' => "La taille '{$tailleSelectionnee}' n'est pas disponible pour le produit '{$product->name}'. Tailles disponibles: " . implode(', ', $taillesClean)])->withInput();
			}

			$prixVenteVendeur = (float) optional($product->pivot)->prix_vente;
			$prixVenteClient = (float) $productData['prix_vente_client'];

			if ($prixVenteClient <= $prixVenteVendeur) {
				return back()->withErrors(['prix_vente_client' => 'Le prix de vente doit être supérieur au prix d\'achat pour avoir une marge bénéfice'])->withInput();
			}

			$margeParPiece = $prixVenteClient - $prixVenteVendeur;
			$margeTotalePieces = $margeParPiece * (int) $productData['quantite_produit'];
			$prixProduit = $prixVenteClient * (int) $productData['quantite_produit'];

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
