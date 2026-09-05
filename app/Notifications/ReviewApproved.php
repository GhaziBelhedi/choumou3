<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApproved extends Notification
{
    use Queueable;

    public function __construct(protected Review $review)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre avis a été publié')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre avis sur « '.$this->review->product->title.' » a été validé et est maintenant visible.')
            ->action('Voir le produit', route('products.show', $this->review->product->slug))
            ->line('Merci pour votre contribution !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_approved',
            'review_id' => $this->review->id,
            'product_id' => $this->review->product_id,
            'product_title' => $this->review->product->title,
            'product_slug' => $this->review->product->slug,
        ];
    }
}
