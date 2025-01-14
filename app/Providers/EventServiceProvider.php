<?php

namespace App\Providers;

use App\Models\Keyword;
use App\Models\MeetingMember;
use App\Models\Protocol;
use App\Models\ProtocolTask;
use App\Observers\KeywordObserver;
use App\Observers\MeetingMemberObserver;
use App\Observers\ProtocolObserver;
use App\Observers\ProtocolTaskObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        MeetingMember::observe(MeetingMemberObserver::class);
        Protocol::observe(ProtocolObserver::class);
        ProtocolTask::observe(ProtocolTaskObserver::class);
        Keyword::observe(KeywordObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
