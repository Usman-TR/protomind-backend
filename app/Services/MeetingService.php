<?php

namespace App\Services;

use App\Models\Meeting;
use App\Models\MeetingMember;
use App\Notifications\SendMeetingNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class MeetingService
{
    public function sendNotification(Meeting $meeting, string $email): void
    {
        Notification::route('mail', $email)
            ->notify(new SendMeetingNotification($meeting));
    }

    public function create(array $data): ?Meeting
    {
        $meeting = null;

        DB::transaction(function() use ($data, &$meeting) {
            $meeting = Meeting::create($data);

            if(isset($data['document']) && $data['document']) {
                $meeting->addMedia($data['document'])->toMediaCollection('document');
            }

            $currentTime = now();

            foreach($data['member'] as $member) {
                MeetingMember::create([
                    'meeting_id' => $meeting->id,
                    'member_id' => $member['member_id'],
                    'email_sent' => $member['should_notify'],
                    'updated_at' => $currentTime,
                    'created_at' => $currentTime,
                ]);
            }
        });

        return $meeting;
    }

    public function update(Meeting $meeting, array $data)
    {
        $meeting->update($data);

        if(isset($data['document'])) {
            $meeting->clearMediaCollection('document');
            $meeting->addMedia($data['document'])->toMediaCollection('document');
        }

        if (isset($data['members'])) {
            $currentMemberIds = $meeting->members->pluck('id')->toArray();
            $incomingMemberIds = collect($data['members'])->pluck('member_id')->filter()->toArray();

            $idsToDelete = array_diff($currentMemberIds, $incomingMemberIds);
            MeetingMember::destroy($idsToDelete);

            foreach ($data['members'] as $memberData) {
                MeetingMember::onlyTrashed()
                    ->where('member_id', $memberData['member_id'])
                    ->first()
                    ?->restore();

                $member = MeetingMember::query()
                    ->where('member_id', $memberData['member_id'])
                    ->where('meeting_id', $meeting->id)
                    ->first();

                if ($member) {
                    if(!$member->email_sent) {
                        $member->update([
                            'email_sent' => $memberData['should_notify'],
                        ]);
                    }
                } else {
                    MeetingMember::create([
                        'meeting_id' => $meeting->id,
                        'email_sent' => $memberData['should_notify'],
                        'member_id' => $memberData['member_id'],
                    ]);
                }
            }
        }
    }
}
