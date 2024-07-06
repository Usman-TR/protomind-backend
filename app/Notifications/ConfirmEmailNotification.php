<?php

namespace App\Notifications;

use App\Mail\ConfirmEmailMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ConfirmEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly string $password
    )
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): ConfirmEmailMail
    {
        return (new ConfirmEmailMail($notifiable->email, $this->password))->to($notifiable->email);
    }

}
