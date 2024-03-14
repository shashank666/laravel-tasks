<?php

namespace App\Jobs\AndroidPush;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Helpers\SendAndroidPush;
use App\Model\User;

class CommentLikeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $sender;
    protected $fcm_tokens;
    protected $short_comment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $sender,$fcm_tokens, $short_comment)
    {
        $this->sender= $sender;
        $this->fcm_tokens=$fcm_tokens;
        $this->short_comment=$short_comment;
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
            'body'         => $this->sender->name.' Liked Your Comment: '.$this->short_comment->comment,
            'click_action' => 'OPINION'
        ];
        $data['data']=[
            'event' => 'LIKED_COMMENT',
            'opinion_id'=>$this->short_comment->short_opinion_id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:'',
            'title'        => 'OPINED',
            'body'         =>$this->sender->name.' Liked Your Comment: '.$this->short_comment->comment,
            'click_action' => 'OPINION'
        ];
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
