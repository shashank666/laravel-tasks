<?php

namespace App\Listeners;

use App\Events\UserProfileViewCounterEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Model\User;

class UserProfileViewCounterEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserProfileViewCounterEvent  $event
     * @return void
     */
    public function handle(UserProfileViewCounterEvent $event)
    {
        $user = User::find($event->user->id);
        $user->views=$event->user->views+1;
        $user->save();
    }
}
