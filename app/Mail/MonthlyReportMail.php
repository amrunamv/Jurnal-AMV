<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $month,
        public string $pdfOutput,
        public string $siteName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Laporan Aktivitas Bulanan - {$this->month} - {$this->siteName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.monthly-report',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfOutput, "Laporan-Bulanan-{$this->month}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
