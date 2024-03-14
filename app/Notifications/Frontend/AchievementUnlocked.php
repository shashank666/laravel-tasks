<?php

namespace App\Notifications\Frontend;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Model\Achievement;
use Illuminate\Support\Str;
use App\Model\User;
use App\Model\ShortOpinionLike;
use App\Http\Helpers\FcmMessage;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;


class AchievementUnlocked extends Notification implements ShouldQueue
{
    use Queueable;
    protected $achievement;
    // protected $sender;
    protected $fcm_tokens;
  

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Achievement $achievement,User $sender,$fcm_tokens)
    {
        $this->achievement=$achievement;
        $this->fcm_tokens=$fcm_tokens;
      
    }

    public function via($notifiable)
    {
        return ['database',WebPushChannel::class];
        //,'fcm'
    }


    public function toDatabase($notifiable)
    {



        if($this->achievement->title!=null){
            $notification_text=$this->achievement->title. ' Achievement Unlocked '.'You get '.$this->achievement->reward.' points';
        }else{
            //echo "Achievement Title is null";
            $notification_text = 'Achievement Unlocked ';
        }



        return [
            'event'=>'ACHIEVEMENT_UNLOCKED',
            'sender_id'=> 0,
            'data'=>array('achievementId'=>$this->achievement->achievement_id,'title'=>$this->achievement->title),
            'notification'=>array(
                'message'=> $notification_text,
                'action_url'=> route('opinion',['username'=>$this->opinion->user['username'],'id'=>$this->opinion->uuid])
                )
            ];
    }

    public function toFCM($notifiable){
        if($this->achievement->title!=null){
            $notification_text=$this->achievement->title. ' Achievement Unlocked '.'You get '.$this->achievement->reward.' points';
        }else{
            //echo "Achievement Title is null";
            $notification_text = 'Achievement Unlocked ';
        }


        $message=new FcmMessage();
        $message->to($this->fcm_tokens)->content([
            'title'        => 'Achievement Unlocked',
            'body'         => $notification_text,
            'click_action' => 'OPINION'
        ])->data([
            'event' => 'OPINION_LIKED',
            'achievement_id'=>$this->achievement->achievement_id,
            'opinion_plainbody'=>$this->achievement->title!=null?$this->achievement->title:'',
            'sender_id'=>0,
            'sender_name'=>'admin',
            'sender_image'=>''
        ])->priority(FcmMessage::PRIORITY_HIGH)->contentAvailable(true);
        return $message;
    }

    public function toWebPush($notifiable, $notification)
    {
        $notification_text='Achievement Unlocked';
        // if($this->opinion->user->id==$notifiable->id)
        // {
        //     $notification_text='Liked Your Opinion';
        // }

        return (new WebPushMessage)
            ->title($this->sender->name.' '.$notification_text)
            ->icon('https://www.weopined.com/favicon.png')
            ->body(str::limit($this->opinion->plain_body,50,'...'))
            ->tag("Opined")
            ->requireInteraction(true)
            ->renotify(true)
            ->data(['goto'=>route('opinion',['username'=>$this->opinion->user['username'],'id'=>$this->opinion->uuid])]);
    }


}
