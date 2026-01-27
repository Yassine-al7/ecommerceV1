<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\AdminMessage;
use App\Notifications\ColorStockAlertNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ColorStockNotificationService
{
    /**
     * Notifier immédiatement les vendeurs et admins d'un changement de stock
     */
    public function notifyStockChange(Product $product, string $colorName, int $oldQuantity, int $newQuantity): void
    {
        try {
            // Déterminer le type d'alerte
            $alertType = $this->determineAlertType($oldQuantity, $newQuantity);
            $priority = $this->determinePriority($alertType);

            // Créer le message admin
            $this->createAdminMessage($product, $colorName, $oldQuantity, $newQuantity, $alertType, $priority);

            // Notifier les utilisateurs concernés
            $this->notifyUsers($product, $colorName, $oldQuantity, $newQuantity, $alertType, $priority);

            // Logger l'action
            $this->logStockChange($product, $colorName, $oldQuantity, $newQuantity, $alertType);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la notification de changement de stock: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'color_name' => $colorName,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity
            ]);
        }
    }

    /**
     * Déterminer le type d'alerte
     */
    private function determineAlertType(int $oldQuantity, int $newQuantity): string
    {
        if ($oldQuantity > 0 && $newQuantity <= 0) {
            return 'out_of_stock';
        } elseif ($oldQuantity > 5 && $newQuantity <= 5 && $newQuantity > 0) {
            return 'low_stock';
        } elseif ($oldQuantity <= 0 && $newQuantity > 0) {
            return 'stock_restored';
        } else {
            return 'stock_change';
        }
    }

    /**
     * Déterminer la priorité
     */
    private function determinePriority(string $alertType): string
    {
        switch ($alertType) {
            case 'out_of_stock':
                return 'urgent';
            case 'low_stock':
                return 'high';
            case 'stock_restored':
                return 'medium';
            default:
                return 'low';
        }
    }

    /**
     * Créer le message admin
     */
    private function createAdminMessage(Product $product, string $colorName, int $oldQuantity, int $newQuantity, string $alertType, string $priority): void
    {
        $title = $this->getAdminMessageTitle($alertType, $product->name);
        $message = $this->getAdminMessageContent($alertType, $product->name, $colorName, $newQuantity);

        AdminMessage::create([
            'title' => $title,
            'message' => $message,
            'type' => $this->getAdminMessageType($alertType),
            'priority' => $priority,
            'is_active' => true,
            'target_roles' => ['seller', 'admin'],
            'expires_at' => now()->addDays(7), // Expire après 7 jours
        ]);
    }

    /**
     * Obtenir le titre du message admin
     */
    private function getAdminMessageTitle(string $alertType, string $productName): string
    {
        switch ($alertType) {
            case 'out_of_stock':
                return '🚨 RUPTURE DE STOCK - ' . $productName;
            case 'low_stock':
                return '⚠️ STOCK FAIBLE - ' . $productName;
            case 'stock_restored':
                return '✅ STOCK RESTAURÉ - ' . $productName;
            default:
                return 'ℹ️ Changement de stock - ' . $productName;
        }
    }

    /**
     * Obtenir le contenu du message admin
     */
    private function getAdminMessageContent(string $alertType, string $productName, string $colorName, int $newQuantity): string
    {
        switch ($alertType) {
            case 'out_of_stock':
                return "URGENT : La couleur '{$colorName}' du produit '{$productName}' est maintenant en rupture de stock. Action immédiate requise !";
            case 'low_stock':
                return "ALERTE : La couleur '{$colorName}' du produit '{$productName}' a un stock faible ({$newQuantity} unités). Commande en urgence recommandée.";
            case 'stock_restored':
                return "INFORMATION : La couleur '{$colorName}' du produit '{$productName}' est de nouveau en stock ({$newQuantity} unités).";
            default:
                return "Changement de stock détecté pour la couleur '{$colorName}' du produit '{$productName}'.";
        }
    }

    /**
     * Obtenir le type de message admin
     */
    private function getAdminMessageType(string $alertType): string
    {
        switch ($alertType) {
            case 'out_of_stock':
                return 'error';
            case 'low_stock':
                return 'warning';
            case 'stock_restored':
                return 'success';
            default:
                return 'info';
        }
    }

    /**
     * Notifier les utilisateurs
     */
    private function notifyUsers(Product $product, string $colorName, int $oldQuantity, int $newQuantity, string $alertType, string $priority): void
    {
        // Récupérer tous les vendeurs et admins
        $users = User::whereIn('role', ['seller', 'admin'])
            ->where('is_active', true)
            ->get();

        if ($users->isEmpty()) {
            Log::warning('Aucun utilisateur trouvé pour les notifications de stock');
            return;
        }

        // Créer la notification
        $notification = new ColorStockAlertNotification(
            $product,
            $colorName,
            $oldQuantity,
            $newQuantity,
            $alertType,
            $priority
        );

        // Envoyer les notifications
        Notification::send($users, $notification);

        // Log du nombre de notifications envoyées
        Log::info('Notifications de stock envoyées', [
            'product_id' => $product->id,
            'color_name' => $colorName,
            'alert_type' => $alertType,
            'users_notified' => $users->count(),
            'priority' => $priority
        ]);
    }

    /**
     * Logger le changement de stock
     */
    private function logStockChange(Product $product, string $colorName, int $oldQuantity, int $newQuantity, string $alertType): void
    {
        $logLevel = $this->getLogLevel($alertType);
        $message = "Changement de stock détecté: Produit '{$product->name}', Couleur '{$colorName}', {$oldQuantity} → {$newQuantity} unités";

        Log::channel('stock')->log($logLevel, $message, [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'color_name' => $colorName,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'alert_type' => $alertType,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Obtenir le niveau de log approprié
     */
    private function getLogLevel(string $alertType): string
    {
        switch ($alertType) {
            case 'out_of_stock':
                return 'critical';
            case 'low_stock':
                return 'warning';
            case 'stock_restored':
                return 'info';
            default:
                return 'debug';
        }
    }

    /**
     * Notifier immédiatement pour une rupture de stock critique
     */
    public function notifyCriticalStockOut(Product $product, string $colorName): void
    {
        $this->notifyStockChange($product, $colorName, 1, 0);
        
        // Notification supplémentaire pour les admins
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        
        if ($admins->isNotEmpty()) {
            // Créer une notification d'urgence spéciale
            $urgentNotification = new ColorStockAlertNotification(
                $product,
                $colorName,
                1,
                0,
                'out_of_stock',
                'urgent'
            );
            
            Notification::send($admins, $urgentNotification);
        }
    }

    /**
     * Vérifier et notifier les stocks critiques
     */
    public function checkAndNotifyCriticalStocks(): void
    {
        $products = Product::whereNotNull('stock_couleurs')->get();
        
        foreach ($products as $product) {
            if (is_array($product->stock_couleurs)) {
                foreach ($product->stock_couleurs as $colorStock) {
                    if (is_array($colorStock) && isset($colorStock['name']) && isset($colorStock['quantity'])) {
                        $quantity = $colorStock['quantity'] ?? 0;
                        
                        if ($quantity <= 0) {
                            // Vérifier si on a déjà notifié récemment
                            $recentNotification = AdminMessage::where('title', 'like', '%' . $product->name . '%')
                                ->where('created_at', '>', now()->subHours(24))
                                ->first();
                            
                            if (!$recentNotification) {
                                $this->notifyCriticalStockOut($product, $colorStock['name']);
                            }
                        }
                    }
                }
            }
        }
    }
}
