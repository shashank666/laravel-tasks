<?php

namespace App\Listeners;

use App\Events\PostViewCounterEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Model\Post;
use App\Model\Views;
use Illuminate\Support\Facades\Auth;
use DB;

class PostViewCounterEventListener
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
     * @param  PostViewCounterEvent  $event
     * @return void
     */
    public function handle(PostViewCounterEvent $event)
    {
        /*$exists=DB::table('views')->where(['post_id'=>$event->post->id,'ip_address'=>$event->ip_address])
        ->whereRaw('viewed_at > (NOW() - INTERVAL 24 HOUR)')
        ->get();

        if(count($exists)==0){ */
        $view = new Views();
        $view->post_id=$event->post->id;
        $view->user_id=Auth::check()? auth()->user()->id:NULL;
        $view->ip_address=$event->ip_address;
        $view->save();

        $post = Post::find($event->post->id);
        $post->views=$event->post->views+1;
        $post->save();
        //} 
    }

}
