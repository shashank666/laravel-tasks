<?php

namespace App\Jobs\AndroidPush;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Helpers\SendAndroidPush;
use App\Model\Thread;
use App\Model\User;

class ThreadLikedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $thread;
    protected $sender;
    protected $fcm_tokens;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Thread $thread,User $sender,$fcm_tokens)
    {
        $this->thread=$thread;
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
            'body'         => $this->sender->name.' Liked Thread #'.$this->thread->name,
            'click_action' => 'THREAD'
        ];
        $data['data']=[
            'event' => 'THREAD_LIKED',
            'thread_id'=>$this->thread->id,
            'thread_name'=>$this->thread->name,
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:'',
            'title'        => 'OPINED',
            'body'         => $this->sender->name.' Liked Thread #'.$this->thread->name,
            'click_action' => 'THREAD'
        ];
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
