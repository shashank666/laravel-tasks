<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyAccountMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user,$verify_url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$token)
    {
        $this->user=$user;
        $this->verify_url= "https://www.weopined.com/me/verify/".$token;
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
                    ->subject('Verify Your Email Address')
                    ->view('frontend.email.auth.verifyemail')
                    ->with(['name' =>ucfirst($this->user['name']),
                            'email'=>$this->user['email'],
                            'verify_url'=>$this->verify_url]);

    }
}
