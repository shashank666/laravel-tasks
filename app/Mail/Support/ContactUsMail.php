<?php

namespace App\Mail\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name,$email,$email_subject,$body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($msg)
    {
        $this->name=$msg->name;
        $this->email=$msg->email;
        $this->email_subject=$msg->subject;
        $this->body=$msg->message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to('reach-us@weopined.com')
                    ->from('notification@weopined.com','ContactUs - Opined')
                    ->subject($this->name.' Message From Contact Us on Opined')
                    ->view('frontend.email.support.contactus')
                    ->with(['name' =>ucfirst($this->name),
                            'email'=>$this->email,
                            'subject'=>$this->email_subject,
                            'body'=>$this->body
                            ]);
    }
}
