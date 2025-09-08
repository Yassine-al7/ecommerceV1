<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the seller's assigned products.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé');
        }

        // Récupérer tous les produits assignés avec leurs catégories
        $query = $user->assignedProducts()->with('category');

        $products = $query->get();

        // Filtrer côté serveur après récupération
        if ($request->filled('category')) {
            $products = $products->filter(function ($product) use ($request) {
                return $product->category && $product->category->name === $request->category;
            });
        }



        if ($request->filled('search')) {
            $products = $products->filter(function ($product) use ($request) {
                return stripos($product->name, $request->search) !== false;
            });
        }

        // Récupérer les catégories uniques pour le filtre
        $categories = \App\Models\Category::orderBy('name')->get();

        // Parser les tailles côté serveur pour éviter d'afficher des chaînes brutes dans la vue
        foreach ($products as $p) {
            $raw = $p->tailles;
            $parsed = [];
            if (is_string($raw)) {
                // 1) Tentative JSON
                $tmp = json_decode($raw, true);
                if (is_array($tmp)) {
                    $parsed = $tmp;
                } else {
                    // 2) Extraire contenu entre guillemets
                    if (preg_match_all('/"([^\"]+)"/u', $raw, $m) && !empty($m[1])) {
                        $parsed = $m[1];
                    } else {
                        // 3) Nettoyage + split
                        $clean = trim($raw);
                        // Si c'est littéralement [ , , ] -> vide
                        if (preg_match('/^\s*\[\s*,\s*,?\s*\]\s*$/u', $clean)) {
                            $parsed = [];
                        } else {
                            $clean = trim($clean, "[]\r\n\t \0\x0B");
                            $clean = str_replace(['"','“','”','‟','’','‘'], ' ', $clean);
                            $parts = preg_split('/\s*,\s*|\s*;\s*|\r?\n+/u', $clean);
                            $parsed = array_values(array_filter(array_map('trim', (array)$parts)));
                        }
                    }
                }
            } elseif (is_array($raw)) {
                $parsed = $raw;
            }
            // Filtrer tokens vides ou ponctuation seule
            $parsed = array_filter((array)$parsed, function ($v) {
                $v = trim((string)$v);
                if ($v === '' || $v === ',' || $v === '[' || $v === ']' ) return false;
                $lower = mb_strtolower($v);
                if (in_array($lower, ['null','undefined','n/a','na','none','vide'], true)) return false;
                return (bool) preg_match('/[\p{L}\p{N}]/u', $v);
            });
            $p->tailles_parsed = array_values(array_unique($parsed));
        }

        return view('seller.products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $userId = auth()->id();
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé');
        }

        $product = $user->assignedProducts()
            ->with('category')
            ->select(
                'produits.id',
                'produits.name',
                'produits.image',
                'produits.prix_admin',
                'produits.couleur',
                'produits.tailles',
                'produits.stock_couleurs',
                'produits.quantite_stock',
                'produits.categorie_id'
            )
            ->where('produits.id', $id)
            ->firstOrFail();

        // Normalize fields for view
        $sizes = is_array($product->tailles) ? $product->tailles : (json_decode((string) $product->tailles, true) ?: []);
        $colors = is_array($product->couleur) ? $product->couleur : (json_decode((string) $product->couleur, true) ?: []);
        $stockByColor = is_array($product->stock_couleurs) ? $product->stock_couleurs : (json_decode((string) $product->stock_couleurs, true) ?: []);

        return view('seller.products.show', [
            'product' => $product,
            'sizes' => $sizes,
            'colors' => $colors,
            'stockByColor' => $stockByColor,
        ]);
    }
}
