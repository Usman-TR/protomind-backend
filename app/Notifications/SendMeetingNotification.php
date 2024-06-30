<?php

namespace App\Notifications;

use App\Mail\MeetingInvitationMail;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendMeetingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Meeting $meeting
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MeetingInvitationMail
    {
        return (new MeetingInvitationMail($this->meeting))->to($notifiable->email);
    }
}
