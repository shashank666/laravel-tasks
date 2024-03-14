<?php

namespace App\Jobs\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use App\Mail\Post\PostCreatedMail;

class PostCreatedMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3;
    public $post,$post_author,$followers;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($post,$post_author,$followers)
    {
        $this->post=$post;
        $this->post_author=$post_author;
        $this->followers=$followers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            foreach($this->followers as $follower){
                Mail::send(new PostCreatedMail($follower,$this->post,$this->post_author));
            }
        } catch (Exception $e) { }
    }

    public function failed(Exception $exception)
    {

    }
}
