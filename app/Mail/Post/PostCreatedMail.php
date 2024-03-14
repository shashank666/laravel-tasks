<?php

namespace App\Mail\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostCreatedMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user,$post,$post_author;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$post,$post_author)
    {
        $this->user=$user;
        $this->post=$post;
        $this->post_author=$post_author;
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
                    ->subject($this->post['title'] ." published by ".ucfirst($this->post_author['name']))
                    ->view('frontend.email.post.blogpost')
                    ->with([
                            'post_link'=>'https://www.weopined.com/opinion/'.$this->post['slug'],
                            'post_title'=>$this->post['title'],
                            'post_user'=>ucfirst($this->post_author['name']),
                            'post_userlink'=>'https://www.weopined.com/@'.$this->post_author['username'],
                            'post_createdat'=>$this->post['created_at'],
                            'post_cover'=>$this->post['coverimage'],
                            'post_body'=>str::limit($this->post['plainbody'],200,' ...  read more on Opined')
                            ]);
    }
}
