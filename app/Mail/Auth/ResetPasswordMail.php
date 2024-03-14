<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user,$reset_url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$reset_url)
    {
        $this->user=$user;
        $this->reset_url=$reset_url;
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
                    ->subject('Reset Password for Opined')
                    ->view('frontend.email.auth.reset')
                    ->with(['name' =>ucfirst($this->user['name']),
                            'reset_url'=>$this->reset_url]);
    }
}
