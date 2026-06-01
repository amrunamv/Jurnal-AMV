<?php

namespace App\Notifications;

use App\Models\Manuscript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManuscriptStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Manuscript $manuscript;
    protected string $action;
    protected ?string $message;

    public function __construct(Manuscript $manuscript, string $action, ?string $message = null)
    {
        $this->manuscript = $manuscript;
        $this->action = $action;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = __("common.statuses.{$this->action}") ?? ucfirst($this->action);

        $mail = (new MailMessage)
            ->subject("Update Manuskrip: {$statusLabel} - AMV Open Science")
            ->greeting("Yth. {$notifiable->name},")
            ->replyTo('jurnal@amvsd.id')
            ->line("Manuskrip Anda \"{$this->manuscript->title}\" telah diperbarui.")
            ->line("Status Baru: **{$statusLabel}**");

        if ($this->message) {
            $mail->line($this->message);
        }

        return $mail
            ->action('Lihat Manuskrip', url('/console/manuscripts/' . $this->manuscript->slug))
            ->line('Terima kasih telah mengirimkan ke AMV Open Science.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'manuscript_id' => $this->manuscript->id,
            'manuscript_title' => $this->manuscript->title,
            'action' => $this->action,
            'status' => $this->manuscript->status,
            'message' => $this->message,
        ];
    }
}
