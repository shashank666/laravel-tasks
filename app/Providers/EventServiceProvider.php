<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\PostViewCounterEvent' => [
            'App\Listeners\PostViewCounterEventListener',
        ],
        'App\Events\OpinionViewCounterEvent' => [
            'App\Listeners\OpinionViewCounterEventListener',
        ],
        'App\Events\ThreadViewCounterEvent' => [
            'App\Listeners\ThreadViewCounterEventListener',
        ],
        'App\Events\UserProfileViewCounterEvent' => [
            'App\Listeners\UserProfileViewCounterEventListener',
        ],
    ];

    protected $subscribe = [
        'App\Listeners\UserEventSubscriber',
    ];
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
