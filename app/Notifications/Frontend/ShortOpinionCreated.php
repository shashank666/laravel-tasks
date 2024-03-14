<?php

namespace App\Notifications\Frontend;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;
use App\Model\User;
use App\Model\ShortOpinion;
use App\Http\Helpers\FcmMessage;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;


class ShortOpinionCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $sender;
    protected $opinion;
    protected $fcm_tokens;

    public function __construct(ShortOpinion $opinion,User $sender,$fcm_tokens)
    {
        $this->opinion=$opinion;
        $this->sender=$sender;
        $this->fcm_tokens=$fcm_tokens;
    }


    public function via($notifiable)
    {
        return ['database',WebPushChannel::class];
        //,'fcm'
    }

    public function toDatabase($notifiable)
    {
        if($this->opinion->plain_body!=null){
            $notification_text='Published Opinion : '.str::limit($this->opinion->plain_body,50,'...');
        }else{
            $notification_text='Published Opinion';
        }

        return [
            'event'=>'OPINION_CREATED',
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
            $notification_text='Published Opinion : '.str::limit($this->opinion->plain_body,50,'...');
        }else{
            $notification_text='Published Opinion';
        }

        $message=new FcmMessage();
        $message->to($this->fcm_tokens)->content([
            'title'        => 'OPINED',
            'body'         => $notification_text,
            'click_action' => 'OPINION'
        ])->data([
            'event' => 'OPINION_CREATED',
            'opinion_id'=>$this->opinion->id,
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:''
        ])->priority(FcmMessage::PRIORITY_HIGH)->contentAvailable(true);
        return $message;
    }

    public function toWebPush($notifiable, $notification)
    {
        $notification_text='Published Opinion';

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
