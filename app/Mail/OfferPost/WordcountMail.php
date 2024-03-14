<?php

namespace App\Mail\OfferPost;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WordcountMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user,$post;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$post)
    {
        $this->user=$user;
        $this->post=$post;
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
                    ->subject('Regarding Your Article On Opined')
                    ->view('frontend.email.offerpost.wordcount')
                    ->with(['name' =>ucfirst($this->user['name']),
                            'post_link'=>'https://www.weopined.com/opinion/'.$this->post['slug'],
                            'post_title'=>$this->post['title']
                            ]);
    }
}
