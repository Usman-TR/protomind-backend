<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $email,
        private readonly string $password
    )
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Protomind: Успешная регистрация.',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.confirm_email',
            with: [
                'email' => $this->email,
                'password' => $this->password,
            ],
        );
    }
}
