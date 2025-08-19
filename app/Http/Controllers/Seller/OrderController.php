<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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
		// Produits assignés au vendeur
		$products = auth()->user()->assignedProducts()->select('produits.id','produits.name','produits.tailles')->get();
		return view('seller.order_form', compact('products'));
	}

	public function store(Request $request)
	{
		$data = $request->validate([
			'reference' => 'required|string',
			'nom_client' => 'required|string',
			'ville' => 'required|string',
			'adresse_client' => 'required|string',
			'numero_telephone_client' => 'required|string',
			'product_id' => 'required|exists:produits,id',
			'taille_produit' => 'required|string|max:50',
			'quantite_produit' => 'required|integer|min:1',
			'commentaire' => 'nullable|string',
		]);
		// Calcul des prix à partir du pivot assigné
		$product = auth()->user()->assignedProducts()->where('produits.id', $data['product_id'])->firstOrFail();
		// Vérifier que la taille choisie fait partie des tailles définies par l'admin (si des tailles sont définies)
		$availableRaw = $product->tailles;
		$availableSizes = is_string($availableRaw) ? json_decode($availableRaw, true) : (array) $availableRaw;
		$availableSizes = is_array($availableSizes) ? array_map(static function ($v) { return trim((string) $v); }, $availableSizes) : [];
		$availableSizes = array_values(array_filter($availableSizes, static function ($v) { return $v !== ''; }));
		if (!empty($availableSizes)) {
			if (!in_array(trim((string)$data['taille_produit']), $availableSizes, true)) {
				return back()->withErrors(['taille_produit' => 'Taille invalide pour ce produit'])->withInput();
			}
		}
		// Si aucune taille n'est définie, accepter n'importe quelle taille (produit "taille unique")
		$prixVente = (float) optional($product->pivot)->prix_vente;
		$data['prix_produit'] = $prixVente;
		$data['prix_commande'] = $prixVente * (int) $data['quantite_produit'];
		$data['produits'] = json_encode([['product_id' => $data['product_id'], 'qty' => (int) $data['quantite_produit']]]);
		$data['status'] = 'en attente';
		$data['seller_id'] = auth()->id();
		Order::create($data);

		return redirect()->route('seller.orders.index')->with('success', 'Commande créée (en attente).');
	}

	public function show($id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
		return view('seller.order_detail', compact('order'));
	}

	public function edit($id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
		$products = auth()->user()->assignedProducts()->select('produits.id','produits.name','produits.tailles')->get();
		return view('seller.order_form', compact('order','products'));
	}

	public function update(Request $request, $id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();

		$data = $request->validate([
			'reference' => 'required|string',
			'nom_client' => 'required|string',
			'ville' => 'required|string',
			'adresse_client' => 'required|string',
			'numero_telephone_client' => 'required|string',
			'product_id' => 'required|exists:produits,id',
			'taille_produit' => 'required|string|max:50',
			'quantite_produit' => 'required|integer|min:1',
			'commentaire' => 'nullable|string',
		]);
		$product = auth()->user()->assignedProducts()->where('produits.id', $data['product_id'])->firstOrFail();
		// Vérifier que la taille choisie fait partie des tailles définies par l'admin (si des tailles sont définies)
		$availableRaw = $product->tailles;
		$availableSizes = is_string($availableRaw) ? json_decode($availableRaw, true) : (array) $availableRaw;
		$availableSizes = is_array($availableSizes) ? array_map(static function ($v) { return trim((string) $v); }, $availableSizes) : [];
		$availableSizes = array_values(array_filter($availableSizes, static function ($v) { return $v !== ''; }));
		if (!empty($availableSizes)) {
			if (!in_array(trim((string)$data['taille_produit']), $availableSizes, true)) {
				return back()->withErrors(['taille_produit' => 'Taille invalide pour ce produit'])->withInput();
			}
		}
		// Si aucune taille n'est définie, accepter n'importe quelle taille (produit "taille unique")
		$prixVente = (float) optional($product->pivot)->prix_vente;
		$data['prix_produit'] = $prixVente;
		$data['prix_commande'] = $prixVente * (int) $data['quantite_produit'];
		$data['produits'] = json_encode([['product_id' => $data['product_id'], 'qty' => (int) $data['quantite_produit']]]);
		$order->update($data);

		return redirect()->route('seller.orders.index')->with('success', 'Commande mise à jour.');
	}

	public function updateStatus(Request $request, $id)
	{
		$order = Order::where('id', $id)->where('seller_id', auth()->id())->firstOrFail();
		$order->status = $request->input('status');
		$order->save();

		return redirect()->route('seller.orders.index')->with('success', 'Order status updated successfully.');
	}


}
