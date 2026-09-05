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
            ->line('Votre avis sur « '.$this->review->book->title.' » a été validé et est maintenant visible.')
            ->action('Voir le livre', route('books.show', $this->review->book->slug))
            ->line('Merci pour votre contribution !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_approved',
            'review_id' => $this->review->id,
            'book_id' => $this->review->book_id,
            'book_title' => $this->review->book->title,
            'book_slug' => $this->review->book->slug,
        ];
    }
}
