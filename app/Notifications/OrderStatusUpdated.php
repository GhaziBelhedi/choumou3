<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(protected Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mise à jour de votre commande '.$this->order->order_number)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Le statut de votre commande '.$this->order->order_number.' a été mis à jour.')
            ->line('Nouveau statut : '.$this->order->statusLabel())
            ->action('Voir ma commande', route('orders.show', $this->order))
            ->line('Merci de votre confiance !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_status_updated',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'status_label' => $this->order->statusLabel(),
        ];
    }
}
