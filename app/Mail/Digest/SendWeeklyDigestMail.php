<?php

namespace App\Mail\Digest;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWeeklyDigestMail extends Mailable
//implements ShouldQueue
{
    //Queueable,
    use  SerializesModels;
    public $opinions,$latest_posts,$trending_threads,$user;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($opinions,$latest_posts,$trending_threads,$user)
    {
        $this->opinions=$opinions;
        $this->latest_posts=$latest_posts;
        $this->trending_threads=$trending_threads;
        $this->user=$user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->user['email'])
        ->from('notification@weopined.com','Opined')
        ->subject("What's New on Opined")
        ->view('frontend.email.digest.weekly')
        ->with(['user' =>$this->user,
                'opinions'=>$this->opinions,
                'latest_posts'=>$this->latest_posts,
                'trending_threads'=>$this->trending_threads
                ]);
    }
}
