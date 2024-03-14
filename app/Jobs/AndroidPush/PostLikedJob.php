<?php

namespace App\Jobs\AndroidPush;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Http\Helpers\SendAndroidPush;
use App\Model\User;
use App\Model\Post;

class PostLikedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;
    protected $sender;
    protected $fcm_tokens;


    public function __construct(Post $post,User $sender,$fcm_tokens)
    {
        $this->post=$post;
        $this->sender=$sender;
        $this->fcm_tokens=$fcm_tokens;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data['priority']='high';
        $data['registration_ids']=$this->fcm_tokens;
        $data['notification']=[
            'title'        => 'OPINED',
            'body'         => $this->sender->name.' Liked Article : '.$this->post->title,
            'image'        =>  $this->post->coverimage,
            'click_action' => 'ARTICLE'
        ];
        $data['data']=[
            'event' => 'ARTICLE_LIKED',
            'post_id'=>$this->post->id,
            'post_title'=>$this->post->title,
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:'',
            'title'        => 'OPINED',
            'body'         => $this->sender->name.' Liked Article : '.$this->post->title,
            'image'        =>  $this->post->coverimage,
            'click_action' => 'ARTICLE'
        ];
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
