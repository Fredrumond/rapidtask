<?php

namespace App\Mail;

use App\Models\TimeMembroConvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConviteTimeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public TimeMembroConvite $convite,
        public string $aceitarUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Convite para o RapidTask',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.convite-time',
        );
    }
}
