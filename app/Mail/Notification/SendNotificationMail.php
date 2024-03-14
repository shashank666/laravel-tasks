<?php

namespace App\Mail\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotificationMail extends Mailable
//implements ShouldQueue
{
    //Queueable,
    use  SerializesModels;
    public $opinions,$user;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($opinions,$user)
    {
        $this->opinions=$opinions;
       
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
        ->subject("Stay Safe Stay Healthy")
        ->view('frontend.email.notification.daily')
        ->with(['user' =>$this->user,
                'opinions'=>$this->opinions
                ]);
    }
}
