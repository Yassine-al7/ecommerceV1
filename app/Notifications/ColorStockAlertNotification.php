<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class ColorStockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $colorName;
    public $oldQuantity;
    public $newQuantity;
    public $alertType;
    public $priority;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product, string $colorName, int $oldQuantity, int $newQuantity, string $alertType, string $priority = 'high')
    {
        $this->product = $product;
        $this->colorName = $colorName;
        $this->oldQuantity = $oldQuantity;
        $this->newQuantity = $newQuantity;
        $this->alertType = $alertType;
        $this->priority = $priority;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->getSubject();
        $message = $this->getMessage();
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting('Alerte Stock - ' . $this->product->name)
            ->line($message)
            ->line('Produit: ' . $this->product->name)
            ->line('Couleur: ' . $this->colorName)
            ->line('Ancienne quantité: ' . $this->oldQuantity)
            ->line('Nouvelle quantité: ' . $this->newQuantity)
            ->action('Voir le produit', url('/admin/color-stock/' . $this->product->id))
            ->priority($this->getMailPriority())
            ->salutation('Cordialement, Système de Gestion des Stocks');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'color_name' => $this->colorName,
            'old_quantity' => $this->oldQuantity,
            'new_quantity' => $this->newQuantity,
            'alert_type' => $this->alertType,
            'priority' => $this->priority,
            'message' => $this->getMessage(),
            'timestamp' => now()->toISOString(),
            'action_url' => '/admin/color-stock/' . $this->product->id
        ];
    }

    /**
     * Get the notification subject.
     */
    private function getSubject(): string
    {
        switch ($this->alertType) {
            case 'out_of_stock':
                return '🚨 URGENT: Rupture de stock - ' . $this->product->name;
            case 'low_stock':
                return '⚠️ Alerte: Stock faible - ' . $this->product->name;
            case 'stock_restored':
                return '✅ Stock restauré - ' . $this->product->name;
            default:
                return 'Alerte Stock - ' . $this->product->name;
        }
    }

    /**
     * Get the notification message.
     */
    private function getMessage(): string
    {
        switch ($this->alertType) {
            case 'out_of_stock':
                return "La couleur '{$this->colorName}' du produit '{$this->product->name}' est maintenant en rupture de stock. Action immédiate requise !";
            case 'low_stock':
                return "La couleur '{$this->colorName}' du produit '{$this->product->name}' a un stock faible ({$this->newQuantity} unités). Commande en urgence recommandée.";
            case 'stock_restored':
                return "La couleur '{$this->colorName}' du produit '{$this->product->name}' est de nouveau en stock ({$this->newQuantity} unités).";
            default:
                return "Changement de stock détecté pour la couleur '{$this->colorName}' du produit '{$this->product->name}'.";
        }
    }

    /**
     * Get the mail priority.
     */
    private function getMailPriority(): int
    {
        switch ($this->priority) {
            case 'urgent':
                return 1; // Highest priority
            case 'high':
                return 2;
            case 'medium':
                return 3;
            default:
                return 4;
        }
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend($notifiable): bool
    {
        // Ne pas envoyer de notifications par email pour les alertes de stock restauré
        if ($this->alertType === 'stock_restored' && $this->via($notifiable) === ['mail']) {
            return false;
        }
        
        return true;
    }
}
