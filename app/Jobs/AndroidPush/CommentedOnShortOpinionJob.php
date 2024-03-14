<?php

namespace App\Jobs\AndroidPush;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Helpers\SendAndroidPush;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\User;

class CommentedOnShortOpinionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $opinion;
    protected $comment;
    protected $sender;
    protected $fcm_tokens;

    public function __construct(ShortOpinion $opinion,ShortOpinionComment $comment,User $sender,$fcm_tokens)
    {
        $this->opinion=$opinion;
        $this->comment=$comment;
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
            'body'         => $this->sender->name.' Commented On Opinion',
            'click_action' => 'OPINION_COMMENTS'
        ];
        $data['data']=[
            'event' => 'COMMENTED_ON_OPINION',
            'opinion_id'=>$this->opinion->id,
            'comment_id'=>$this->comment->id,
            'comment'=>$this->comment->comment,
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:'',
            'title'        => 'OPINED',
            'body'         => $this->sender->name.' Commented On Opinion',
            'click_action' => 'OPINION_COMMENTS'
        ];
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
