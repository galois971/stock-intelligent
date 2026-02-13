<?php

namespace App\Notifications;

use App\Models\StockAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(StockAlert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->alert->product;
        $alertTypeLabels = [
            'low_stock' => 'Stock Minimum Atteint',
            'overstock' => 'Surstock Détecté',
            'risk_of_rupture' => 'Risque de Rupture',
            'expiration' => 'Expiration Proche',
        ];

        $alertLabel = $alertTypeLabels[$this->alert->alert_type] ?? 'Alerte de Stock';

        return (new MailMessage)
            ->subject('🚨 ' . $alertLabel . ' - ' . $product->name)
            ->greeting('Bonjour,')
            ->line('Une alerte de stock a été générée pour le produit suivant :')
            ->line('**Produit :** ' . $product->name)
            ->line('**Code-barres :** ' . $product->barcode)
            ->line('**Type d\'alerte :** ' . $alertLabel)
            ->line('**Stock actuel :** ' . $this->alert->current_quantity)
            ->line('**Stock minimum :** ' . $product->stock_min)
            ->line('**Stock optimal :** ' . $product->stock_optimal)
            ->line('**Message :** ' . $this->alert->message)
            ->action('Voir le produit', url('/products/' . $product->id))
            ->line('Merci de prendre les mesures nécessaires.')
            ->salutation('Cordialement, Système de Gestion de Stock');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'product_id' => $this->alert->product_id,
            'alert_type' => $this->alert->alert_type,
            'message' => $this->alert->message,
        ];
    }
}
