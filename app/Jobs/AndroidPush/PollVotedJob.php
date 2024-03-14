<?php

namespace App\Jobs\AndroidPush;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Helpers\SendAndroidPush;
use App\Model\User;

class PollVotedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $sender;
    protected $fcm_tokens;
    protected $poll_id;
    protected $poll_title;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $sender,$fcm_tokens, $poll_id, $poll_title)
    {
        $this->sender= $sender;
        $this->fcm_tokens=$fcm_tokens;
        $this->poll_id=$poll_id;
        $this->poll_title=$poll_title;
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
            'body'         => $this->sender->name.' Voted For Your Poll : '.$this->poll_title,
            'click_action' => 'USER_PROFILE'
        ];
        $data['data']=[
            'event' => 'ADDED_VOTE',
            'poll_id'=>$this->poll_id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:'',
            'title'        => 'OPINED',
            'body'         => $this->sender->name.' Voted on your Poll : '.$this->poll_title,
            'click_action' => 'USER_PROFILE'
        ];
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
