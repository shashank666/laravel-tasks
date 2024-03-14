<?php

namespace App\Notifications\Frontend;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Model\User;
use App\Http\Helpers\FcmMessage;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;


class pollVoted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sender;
    protected $fcm_tokens;


    public function __construct(User $sender,$fcm_tokens)
    {
        $this->sender= $sender;
        $this->fcm_tokens=$fcm_tokens;
    }


    public function via($notifiable)
    {
        return ['database',WebPushChannel::class];
        //,'fcm'
    }

    public function toDatabase($notifiable)
    {
        return [
            'event'=>'ADDED_VOTE',
            'sender_id'=> $this->sender->id,
            'data'=>array('follower_id'=>$this->sender->id),
            'notification'=>array(
                'message'=>'Voted For Your Poll',
                'action_url'=> route('user_profile',['username'=>$this->sender->username])
                )
        ];
    }

    public function toFCM($notifiable){
        $message=new FcmMessage();
        $message->to($this->fcm_tokens)->content([
            'title'        => 'OPINED',
            'body'         => 'Added You in Circle',
            'click_action' => 'USER_PROFILE'
        ])->data([
            'event' => 'ADDED_VOTE',
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:''
        ])->priority(FcmMessage::PRIORITY_HIGH)->contentAvailable(true);
        return $message;
    }

    public function toWebPush($notifiable, $notification)
    {
        $notification_text='Voted Your Poll';

        return (new WebPushMessage)
            ->title($this->sender->name.' '.$notification_text)
            ->icon('https://www.weopined.com/favicon.png')
            ->body('See Profile of '.$this->sender->name)
            ->tag("Opined")
            ->requireInteraction(true)
            ->renotify(true)
            ->data(['goto'=>route('user_profile',['username'=>$this->sender->username])]);
    }


}
