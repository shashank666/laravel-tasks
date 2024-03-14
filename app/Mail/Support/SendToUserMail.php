<?php

namespace App\Mail\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendToUserMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name,$to_mail,$subject,$content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$to,$subject,$content)
    {
        $this->name=$name;
        $this->to_mail=$to;
        $this->subject=$subject;
        $this->content=$content;
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
                    ->view('admin.email.send')
                    ->with(['name' =>ucfirst($this->name),
                            'subject'=>$this->subject,
                            'content'=>$this->content
                            ]);
    }
}
