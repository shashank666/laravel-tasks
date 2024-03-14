<?php

namespace App\Jobs\AndroidPush;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\ShortOpinionLike;


use App\Http\Helpers\SendAndroidPush;
use App\Model\User;
use App\Model\ShortOpinion;
use Illuminate\Support\Str;

class ShortOpinionLikedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $opinion;
    protected $sender;
    protected $fcm_tokens;
    protected $liked;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ShortOpinion $opinion,User $sender,$fcm_tokens,ShortOpinionLike $liked)
    {
        $this->opinion=$opinion;
        $this->sender=$sender;
        $this->fcm_tokens=$fcm_tokens;
        $this->liked=$liked;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->opinion->plain_body!=null){
            if($this->liked->Agree_Disagree=='1'){
                $notification_text=$this->sender->name.' Agreed to Opinion : '.Str::limit($this->opinion->plain_body,50,'...');
            }else{
                $notification_text=$this->sender->name.' Disagreed to Opinion : '.Str::limit($this->opinion->plain_body,50,'...');
            }
           
        }else{
            if($this->liked->Agree_Disagree=='1'){
                $notification_text=$this->sender->name.' Agreed to your Opinion';
            }else{
                $notification_text=$this->sender->name.' Disagreed to your Opinion';
            }
            
        }

        $data['priority']='high';
        $data['registration_ids']=$this->fcm_tokens;
        $data['notification']=[
            'title'        => 'OPINED',
            'body'         => $notification_text,
            'click_action' => 'OPINION'
        ];
        $data['data']=[
            'event' => 'OPINION_LIKED',
            'opinion_id'=>$this->opinion->id,
            'opinion_plainbody'=>$this->opinion->plain_body!=null?$this->opinion->plain_body:'',
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:'',
            'title'        => 'OPINED',
            'body'         =>$notification_text,
            'click_action' => 'OPINION'
            
        ];
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
