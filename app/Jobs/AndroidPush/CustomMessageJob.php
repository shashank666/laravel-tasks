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

class CustomMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $title;
    protected $message;
 
    protected $fcm_tokens;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title,$message,$fcm_tokens)
    {
        $this->title=$title;
        $this->message=$message;
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
            'title'        => ''.$this->title,
            'body'         => $this->message,
            'click_action' => 'OPINION'
        ];
        $data['data']=[
            'event' => 'OPINION_LIKED',
            'achievement_id'=>0,
            'opinion_plainbody'=> $this->message,
            'sender_id'=>0,
            'sender_name'=>'admin',
            'sender_image'=>'',
            'title'        => ''.$this->title,
            'body'         =>$this->message,
            'click_action' => 'OPINION'
            
        ];
        
        $androidPush=new SendAndroidPush();
        $androidPush->send($data);
    }
}
