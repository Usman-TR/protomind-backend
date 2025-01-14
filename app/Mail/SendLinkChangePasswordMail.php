<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendLinkChangePasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $url
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Protomind: Сброс пароля',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.send_link_change_password',
            with: [
                'url' => $this->url,
            ],
        );
    }
}
