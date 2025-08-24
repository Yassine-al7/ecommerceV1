<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ColorStockController extends Controller
{
    /**
     * Afficher la liste des produits avec leur stock par couleur
     */
    public function index()
    {
        $products = Product::with(['category'])
            ->whereNotNull('stock_couleurs')
            ->get()
            ->map(function ($product) {
                $product->stock_summary = $product->getStockSummary();
                return $product;
            });

        return view('admin.color_stock.index', compact('products'));
    }

    /**
     * Afficher le détail du stock d'un produit
     */
    public function show(Product $product)
    {
        $product->load('category');
        $stockSummary = $product->getStockSummary();

        return view('admin.color_stock.show', compact('product', 'stockSummary'));
    }

    /**
     * Afficher le formulaire de gestion du stock
     */
    public function edit(Product $product)
    {
        $product->load('category');
        $stockSummary = $product->getStockSummary();
        $categories = Category::all();

        return view('admin.color_stock.edit', compact('product', 'stockSummary', 'categories'));
    }

    /**
     * Mettre à jour le stock par couleur et taille
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'stock_couleurs' => 'required|array',
            'stock_couleurs.*.name' => 'required|string',
            'stock_couleurs.*.quantity' => 'required|integer|min:0',
            'tailles' => 'nullable|array',
            'tailles.*' => 'string',
        ]);

        try {
            // Mettre à jour les tailles si fournies
            if ($request->has('tailles')) {
                $product->tailles = $request->tailles;
            }

            // Mettre à jour le stock par couleur
            $stockCouleurs = $request->stock_couleurs;

            // Calculer le stock total
            $totalStock = 0;
            foreach ($stockCouleurs as $colorStock) {
                $totalStock += (int) $colorStock['quantity'];
            }

            // Mettre à jour le produit
            $product->stock_couleurs = $stockCouleurs;
            $product->quantite_stock = $totalStock;
            $product->save();

            Log::info("Stock mis à jour pour le produit {$product->name}", [
                'stock_couleurs' => $stockCouleurs,
                'total_stock' => $totalStock
            ]);

            return redirect()->route('admin.color_stock.show', $product)
                ->with('success', 'Stock mis à jour avec succès!');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour du stock: " . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Erreur lors de la mise à jour du stock: ' . $e->getMessage());
        }
    }

    /**
     * API pour vérifier la disponibilité d'une couleur et taille
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produits,id',
            'color' => 'required|string',
            'size' => 'nullable|string',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }

        $isAvailable = $product->isColorAndSizeAvailable($request->color, $request->size);
        $availableSizes = $product->getAvailableSizesForColor($request->color);
        $stockQuantity = $product->getStockForColor($request->color);

        return response()->json([
            'available' => $isAvailable,
            'stock_quantity' => $stockQuantity,
            'available_sizes' => $availableSizes,
            'is_accessory' => $product->isAccessory(),
        ]);
    }

    /**
     * API pour obtenir les tailles disponibles pour une couleur
     */
    public function getSizesForColor(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produits,id',
            'color' => 'required|string',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }

        $availableSizes = $product->getAvailableSizesForColor($request->color);
        $stockQuantity = $product->getStockForColor($request->color);

        return response()->json([
            'available_sizes' => $availableSizes,
            'stock_quantity' => $stockQuantity,
            'is_accessory' => $product->isAccessory(),
        ]);
    }

    /**
     * API pour diminuer le stock d'une couleur
     */
    public function decreaseStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produits,id',
            'color' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }

        $currentStock = $product->getStockForColor($request->color);

        if ($currentStock < $request->quantity) {
            return response()->json([
                'error' => 'Stock insuffisant',
                'requested' => $request->quantity,
                'available' => $currentStock
            ], 400);
        }

        $newStock = $product->decreaseColorStock($request->color, $request->quantity);

        return response()->json([
            'success' => true,
            'new_stock' => $newStock,
            'total_stock' => $product->quantite_stock
        ]);
    }

    /**
     * API pour augmenter le stock d'une couleur
     */
    public function increaseStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produits,id',
            'color' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }

        $newStock = $product->increaseColorStock($request->color, $request->quantity);

        return response()->json([
            'success' => true,
            'new_stock' => $newStock,
            'total_stock' => $product->quantite_stock
        ]);
    }
}
