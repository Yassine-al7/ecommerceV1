@props(['product', 'couleur', 'taille', 'quantite'])

@php
    // Fonction utilitaire pour générer des messages d'alerte de stock
    function generateStockAlert($product, $couleur, $taille, $quantite) {
        $alertes = [];

        // Vérifier le stock par couleur
        $stockCouleurs = $product->stock_couleurs ?: [];
        $stockCouleurTrouve = false;
        $stockDisponibleCouleur = 0;

        foreach ($stockCouleurs as $stockCouleur) {
            if (is_array($stockCouleur) && isset($stockCouleur['name']) && $stockCouleur['name'] === $couleur) {
                $stockCouleurTrouve = true;
                $stockDisponibleCouleur = $stockCouleur['quantity'] ?? 0;
                break;
            }
        }

        if (!$stockCouleurTrouve) {
            $alertes[] = [
                'type' => 'danger',
                'message' => "Couleur '{$couleur}' non disponible dans le stock",
                'solution' => 'Ajouter cette couleur au stock ou choisir une autre couleur',
                'icon' => '🚨'
            ];
        } elseif ($stockDisponibleCouleur <= 0) {
            $alertes[] = [
                'type' => 'danger',
                'message' => "Couleur '{$couleur}' en rupture de stock (0 disponible)",
                'solution' => 'Réapprovisionner cette couleur ou choisir une autre couleur',
                'icon' => '🚨'
            ];
        } elseif ($stockDisponibleCouleur < $quantite) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "Couleur '{$couleur}' - Stock insuffisant ({$stockDisponibleCouleur} < {$quantite})",
                'solution' => 'Réduire la quantité ou réapprovisionner',
                'icon' => '⚠️'
            ];
        }

        // Vérifier le stock total du produit
        if ($product->quantite_stock <= 0) {
            $alertes[] = [
                'type' => 'danger',
                'message' => "Produit '{$product->name}' en rupture de stock totale",
                'solution' => 'Réapprovisionner le produit',
                'icon' => '🚨'
            ];
        } elseif ($product->quantite_stock < $quantite) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "Produit '{$product->name}' - Stock total insuffisant ({$product->quantite_stock} < {$quantite})",
                'solution' => 'Réduire la quantité ou réapprovisionner',
                'icon' => '⚠️'
            ];
        }

        return $alertes;
    }

    $alertes = generateStockAlert($product, $couleur, $taille, $quantite);
@endphp

@if(!empty($alertes))
    <div class="space-y-3">
        @foreach($alertes as $alerte)
            <div class="rounded-lg border p-4 {{ $alerte['type'] === 'danger' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }}">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <span class="text-2xl">{{ $alerte['icon'] }}</span>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-medium {{ $alerte['type'] === 'danger' ? 'text-red-800' : 'text-yellow-800' }}">
                            {{ $alerte['message'] }}
                        </h4>
                        <div class="mt-2 text-sm {{ $alerte['type'] === 'danger' ? 'text-red-700' : 'text-yellow-700' }}">
                            <p class="flex items-center">
                                <i class="fas fa-lightbulb mr-2 text-{{ $alerte['type'] === 'danger' ? 'red' : 'yellow' }}-600"></i>
                                {{ $alerte['solution'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="rounded-lg border border-green-200 bg-green-50 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    Stock suffisant pour cette commande
                </p>
                <p class="text-sm text-green-700 mt-1">
                    La couleur et la quantité demandées sont disponibles
                </p>
            </div>
        </div>
    </div>
@endif
