<?php

namespace App\Mail\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReplyToUserMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name,$to_mail,$subject,$message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$to,$subject,$message)
    {
        $this->name=$name;
        $this->to_mail=$to;
        $this->subject=$subject;
        $this->message=$message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->to_mail)
                    ->from('notification@weopined.com','Opined')
                    ->subject($this->subject)
                    ->view('admin.email.reply')
                    ->with(['name' =>ucfirst($this->name),
                            'subject'=>$this->subject,
                            'message'=>$this->message
                            ]);
    }
}
