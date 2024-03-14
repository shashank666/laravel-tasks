<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmailAccountMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user,$email_otp;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$email_otp)
    {
        $this->user=$user;
        $this->email_otp= $email_otp;
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
                    ->view('frontend.email.auth.verify_account')
                    ->with(['name' =>ucfirst($this->user['name']),
                            'email'=>$this->user['email'],
                            'email_otp'=>$this->email_otp]);

    }
}
