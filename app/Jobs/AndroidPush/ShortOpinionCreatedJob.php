<?php

namespace App\Jobs\AndroidPush;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Helpers\SendAndroidPush;
use App\Model\User;
use App\Model\ShortOpinion;
use Illuminate\Support\Str;

use function GuzzleHttp\Psr7\str;

class ShortOpinionCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $sender;
    protected $opinion;
    protected $fcm_tokens;

    public function __construct(ShortOpinion $opinion,User $sender,$fcm_tokens)
    {
        $this->opinion=$opinion;
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
        if($this->opinion->plain_body!=null){
            $notification_text= $this->sender->name.' Published Opinion : '.Str::limit($this->opinion->plain_body,50,'...');
        }else{
            $notification_text=$this->sender->name.' Published Opinion';
        }

        $data['priority']='high';
        $data['registration_ids']=$this->fcm_tokens;
        $data['notification']=[
            'title'        => 'OPINED',
            'body'         => $notification_text,
            'click_action' => 'OPINION'
        ];
        $data['data']=[
            'event' => 'OPINION_CREATED',
            'opinion_id'=>$this->opinion->id,
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:'',
            'title'        => 'OPINED',
            'body'         => $notification_text,
            'click_action' => 'OPINION'
        ];
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
