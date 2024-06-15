<?php

namespace App\Observers;

use App\Models\MeetingMember;
use App\Services\MeetingService;

class MeetingMemberObserver
{

    public function __construct(
        private readonly MeetingService $meetingService
    )
    {
    }

    /**
     * Handle the MeetingMember "created" event.
     */
    public function created(MeetingMember $meetingMember): void
    {
        if($meetingMember->email_sent) {
            $this->meetingService->sendNotification($meetingMember->meeting, $meetingMember->member->email);
        }
    }

    /**
     * Handle the MeetingMember "updated" event.
     */
    public function updated(MeetingMember $meetingMember): void
    {
        $originalData = $meetingMember->getOriginal();

        if(!$originalData['email_sent'] && $meetingMember->email_sent) {
            $this->meetingService->sendNotification($meetingMember->meeting, $meetingMember->member->email);
        }

    }
}
