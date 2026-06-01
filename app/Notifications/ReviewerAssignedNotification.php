<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewerAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = $this->review->due_date->format('d M Y');
        
        return (new MailMessage)
            ->subject('New Review Assignment - AMV Open Science')
            ->greeting("Dear {$notifiable->name},")
            ->line('You have been invited to review a manuscript for ' . $this->review->manuscript->journal->name . '.')
            ->line("Review Round: {$this->review->round}")
            ->line("Due Date: {$dueDate}")
            ->line('Please log in to accept or decline this invitation.')
            ->action('View Assignment', url('/console/reviews/' . $this->review->id))
            ->line('Thank you for your contribution to academic publishing.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'manuscript_id' => $this->review->manuscript_id,
            'journal_name' => $this->review->manuscript->journal->name,
            'round' => $this->review->round,
            'due_date' => $this->review->due_date->toISOString(),
        ];
    }
}
