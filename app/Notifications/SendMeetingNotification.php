<?php

namespace App\Notifications;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendMeetingNotification extends Notification
{
    use Queueable;

    protected Meeting $meeting;

    public function __construct($meeting)
    {
        $this->meeting = $meeting;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Здравствуйте!')
            ->line('Вы приглашены на совещание')
            ->line("Тема: {$this->meeting->theme}")
            ->action('Ссылка', $this->meeting->link)
            ->line('Благодарим вас за внимание!')
            ->salutation('С уважением, Protomind');
    }
}
