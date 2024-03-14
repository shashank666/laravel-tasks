<?php

namespace App\Notifications\Frontend;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Model\ShortOpinion;
use Illuminate\Support\Str;
use App\Model\User;
use App\Model\ShortOpinionLike;
use App\Http\Helpers\FcmMessage;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;


class ShortOpinionLiked extends Notification implements ShouldQueue
{
    use Queueable;
    protected $opinion;
    protected $sender;
    protected $fcm_tokens;
    protected $liked;

    /**
     * Create a new notification instance.
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

    public function via($notifiable)
    {
        return ['database',WebPushChannel::class];
        //,'fcm'
    }


    public function toDatabase($notifiable)
    {

        if($this->opinion->plain_body!=null){
            if($this->liked->Agree_Disagree=='1'){
                $notification_text=$this->sender->name.' Agreed to Opinion : '.Str::limit($this->opinion->plain_body,50,'...');
            }else{
                $notification_text=$this->sender->name.' Disagreed to Opinion : '.Str::limit($this->opinion->plain_body,50,'...');
            }
           

            // if($this->opinion->user->id==$notifiable->id)
            // {
            //     $notification_text='Liked Your Opinion : '.str::limit($this->opinion->plain_body,50,'...');
            // }

        }else{
            if($this->liked->Agree_Disagree=='1'){
                $notification_text=$this->sender->name.' Agreed to your Opinion';
            }else{
                $notification_text=$this->sender->name.' Disagreed to your Opinion';
            }
        }



        return [
            'event'=>'OPINION_LIKED',
            'sender_id'=> $this->sender->id,
            'data'=>array('opinion_id'=>$this->opinion->id,'opinion_body'=>$this->opinion->body),
            'notification'=>array(
                'message'=> $notification_text,
                'action_url'=> route('opinion',['username'=>$this->opinion->user['username'],'id'=>$this->opinion->uuid])
                )
            ];
    }

    public function toFCM($notifiable){
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

        $message=new FcmMessage();
        $message->to($this->fcm_tokens)->content([
            'title'        => 'OPINED',
            'body'         => $notification_text,
            'click_action' => 'OPINION'
        ])->data([
            'event' => 'OPINION_LIKED',
            'opinion_id'=>$this->opinion->id,
            'opinion_plainbody'=>$this->opinion->plain_body!=null?$this->opinion->plain_body:'',
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:''
        ])->priority(FcmMessage::PRIORITY_HIGH)->contentAvailable(true);
        return $message;
    }

    public function toWebPush($notifiable, $notification)
    {
        $notification_text='Agreed to Opinion';
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
