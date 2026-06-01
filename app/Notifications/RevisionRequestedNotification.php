<?php

namespace App\Notifications;

use App\Models\Manuscript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RevisionRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Manuscript $manuscript;
    protected string $revisionType;
    protected ?string $editorComments;

    public function __construct(Manuscript $manuscript, string $revisionType = 'major', ?string $editorComments = null)
    {
        $this->manuscript = $manuscript;
        $this->revisionType = $revisionType;
        $this->editorComments = $editorComments;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeLabel = $this->revisionType === 'major' ? 'Major Revision' : 'Minor Revision';

        $mail = (new MailMessage)
            ->subject("Revision Required: {$typeLabel} - AMV Open Science")
            ->greeting("Dear {$notifiable->name},")
            ->line("Your manuscript \"{$this->manuscript->title}\" requires a {$typeLabel}.")
            ->line("Journal: {$this->manuscript->journal->name}");

        if ($this->editorComments) {
            $mail->line('**Editor Comments:**')
                ->line($this->editorComments);
        }

        return $mail
            ->action('Submit Revision', url('/console/manuscripts/' . $this->manuscript->slug . '/revise'))
            ->line('Please submit your revised manuscript at your earliest convenience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'manuscript_id' => $this->manuscript->id,
            'manuscript_title' => $this->manuscript->title,
            'revision_type' => $this->revisionType,
            'editor_comments' => $this->editorComments,
        ];
    }
}
