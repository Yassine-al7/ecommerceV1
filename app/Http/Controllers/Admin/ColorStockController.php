<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\AdminMessage;
use App\Services\ColorStockNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ColorStockController extends Controller
{
    protected $notificationService;

    public function __construct(ColorStockNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Afficher la vue de gestion du stock par couleur
     */
    public function index()
    {
        $products = Product::with(['category'])
            ->whereNotNull('stock_couleurs')
            ->get()
            ->filter(function ($product) {
                return is_array($product->stock_couleurs) && !empty($product->stock_couleurs);
            });

        // Grouper par statut de stock
        $productsByStatus = [
            'out_of_stock' => $products->filter(fn($p) => $p->hasOutOfStockColors()),
            'low_stock' => $products->filter(fn($p) => $p->hasLowStockColors()),
            'normal' => $products->filter(fn($p) => !$p->hasOutOfStockColors() && !$p->hasLowStockColors())
        ];

        return view('admin.color_stock.index', compact('productsByStatus'));
    }

    /**
     * Afficher le détail du stock d'un produit
     */
    public function show(Product $product)
    {
        $colorStock = $product->stock_couleurs ?? [];
        $outOfStockColors = $product->getOutOfStockColors();
        $lowStockColors = $product->getLowStockColors();
        $globalStatus = $product->getGlobalStockStatus();

        return view('admin.color_stock.show', compact(
            'product',
            'colorStock',
            'outOfStockColors',
            'lowStockColors',
            'globalStatus'
        ));
    }

    /**
     * Mettre à jour le stock d'une couleur
     */
    public function updateColorStock(Request $request, Product $product)
    {
        $request->validate([
            'color_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
        ]);

        $colorName = $request->input('color_name');
        $quantity = $request->input('quantity');
        $oldQuantity = $product->getColorStockQuantity($colorName);

        try {
            $product->updateColorStock($colorName, $quantity);

            // Notifier immédiatement les vendeurs et admins
            $this->notificationService->notifyStockChange($product, $colorName, $oldQuantity, $quantity);

            return redirect()->back()->with('success', "Stock de la couleur '{$colorName}' mis à jour avec succès! Notifications envoyées.");
        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour du stock couleur: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du stock.');
        }
    }



    /**
     * Obtenir les statistiques du stock par couleur
     */
    public function getStatistics()
    {
        $products = Product::whereNotNull('stock_couleurs')->get();

        $stats = [
            'total_products' => $products->count(),
            'products_with_out_of_stock_colors' => $products->filter(fn($p) => $p->hasOutOfStockColors())->count(),
            'products_with_low_stock_colors' => $products->filter(fn($p) => $p->hasLowStockColors())->count(),
            'total_colors' => 0,
            'out_of_stock_colors' => 0,
            'low_stock_colors' => 0
        ];

        foreach ($products as $product) {
            if (is_array($product->stock_couleurs)) {
                $stats['total_colors'] += count($product->stock_couleurs);
                $stats['out_of_stock_colors'] += count($product->getOutOfStockColors());
                $stats['low_stock_colors'] += count($product->getLowStockColors());
            }
        }

        return response()->json($stats);
    }

    /**
     * Rechercher les produits par couleur
     */
    public function searchByColor(Request $request)
    {
        $colorName = $request->input('color_name');

        if (!$colorName) {
            return redirect()->route('admin.color-stock.index');
        }

        $products = Product::whereNotNull('stock_couleurs')
            ->get()
            ->filter(function ($product) use ($colorName) {
                if (!is_array($product->stock_couleurs)) {
                    return false;
                }

                foreach ($product->stock_couleurs as $colorStock) {
                    if (is_array($colorStock) && isset($colorStock['name'])) {
                        if (stripos($colorStock['name'], $colorName) !== false) {
                            return true;
                        }
                    }
                }
                return false;
            });

        return view('admin.color_stock.search', compact('products', 'colorName'));
    }

    /**
     * Exporter le rapport de stock par couleur
     */
    public function export()
    {
        $products = Product::with(['category'])
            ->whereNotNull('stock_couleurs')
            ->get();

        $csvData = [];
        $csvData[] = ['Produit', 'Catégorie', 'Couleur', 'Quantité', 'Statut'];

        foreach ($products as $product) {
            if (is_array($product->stock_couleurs)) {
                foreach ($product->stock_couleurs as $colorStock) {
                    if (is_array($colorStock) && isset($colorStock['name'])) {
                        $quantity = $colorStock['quantity'] ?? 0;
                        $status = $quantity <= 0 ? 'Rupture' : ($quantity <= 5 ? 'Faible' : 'Normal');

                        $csvData[] = [
                            $product->name,
                            $product->category->name ?? 'N/A',
                            $colorStock['name'],
                            $quantity,
                            $status
                        ];
                    }
                }
            }
        }

        $filename = 'stock_couleurs_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($csvData) {
            $output = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, $filename);
    }
}
