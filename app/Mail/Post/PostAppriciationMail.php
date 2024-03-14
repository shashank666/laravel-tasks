<?php

namespace App\Mail\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostAppriciationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $post;
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
                    ->subject('Thank you '.ucfirst($this->user['name']).' , for submitting your article on Opined!')
                    ->view('frontend.email.post.appriciate')
                    ->with(['name' =>ucfirst($this->user['name']),
                            'post_title'=>$this->post['title'],
                            'post_link'=>'https://www.weopined.com/opinion/'.$this->post['slug']
                            ]);
    }
}
