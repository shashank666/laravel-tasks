<?php

namespace App\Listeners;

use App\Events\ThreadViewCounterEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Model\Thread;

class ThreadViewCounterEventListener
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
     * @param  ThreadViewCounterEvent  $event
     * @return void
     */
    public function handle(ThreadViewCounterEvent $event)
    {
        $thread = Thread::find($event->thread->id);
        $thread->views=$event->thread->views+1;
        $thread->save();
    }
}
