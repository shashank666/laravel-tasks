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
use App\Model\Achievement;
use Illuminate\Support\Str;

class AchievementUnlockedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $achievement;
 
    protected $fcm_tokens;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Achievement $opinion,User $sender,$fcm_tokens)
    {
        $this->achievement=$opinion;
        $this->fcm_tokens=$fcm_tokens;
   
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->achievement->title!=null){
            $notification_text=$this->achievement->title. ' Achievement Unlocked '.'You get '.$this->achievement->reward.' points';
        }else{
            //echo "Achievement Title is null";
            $notification_text = 'Achievement Unlocked ';
        }


        $data['priority']='high';
        $data['registration_ids']=$this->fcm_tokens;
        $data['notification']=[
            'title'        => 'Achievement Unlocked',
            'body'         => $notification_text,
            'click_action' => 'OPINION'
        ];
        $data['data']=[
            'event' => 'OPINION_LIKED',
            'achievement_id'=>$this->achievement->achievement_id,
            'opinion_plainbody'=>$this->achievement->title!=null?$this->achievement->title:'',
            'sender_id'=>0,
            'sender_name'=>'admin',
            'sender_image'=>'',
            'title'        => 'OPINED',
            'body'         =>$notification_text,
            'click_action' => 'OPINION'
            
        ];
        
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
