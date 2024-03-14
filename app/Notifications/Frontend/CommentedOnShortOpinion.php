<?php

namespace App\Notifications\Frontend;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Model\User;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Http\Helpers\FcmMessage;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;


class CommentedOnShortOpinion extends Notification implements ShouldQueue
{
    use Queueable;
    protected $opinion;
    protected $comment;
    protected $sender;
    protected $fcm_tokens;

    public function __construct(ShortOpinion $opinion,ShortOpinionComment $comment,User $sender,$fcm_tokens)
    {
        $this->opinion=$opinion;
        $this->comment=$comment;
        $this->sender=$sender;
        $this->fcm_tokens=$fcm_tokens;
    }

    public function via($notifiable)
    {
        return ['database',WebPushChannel::class];
        //'fcm'
    }

    public function toDatabase($notifiable)
    {

        $notification_text='Commented On Opinion';

        if($this->opinion->user->id==$notifiable->id)
        {
            $notification_text='Commented On Your Opinion';
        }

        return [
            'event'=>'COMMENTED_ON_OPINION',
            'sender_id'=> $this->sender->id,
            'data'=>array('comment_id'=>$this->comment->id,'comment'=>$this->comment->comment,'opinion_id'=>$this->opinion->id),
            'notification'=>array(
                'message'=>$notification_text,
                'action_url'=> route('opinion',['username'=>$this->opinion->user['username'],'id'=>$this->opinion->uuid])
                )
            ];
    }

    public function toFCM($notifiable){
        $message=new FcmMessage();
        $message->to($this->fcm_tokens)->content([
            'title'        => 'OPINED',
            'body'         => 'Commented On Opinion',
            'click_action' => 'OPINION_COMMENTS'
        ])->data([
            'event' => 'COMMENTED_ON_OPINION',
            'opinion_id'=>$this->opinion->id,
            'comment_id'=>$this->comment->id,
            'comment'=>$this->comment->comment,
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:''
        ])->priority(FcmMessage::PRIORITY_HIGH)->contentAvailable(true);
        return $message;
    }

    public function toWebPush($notifiable, $notification)
    {
        $notification_text='Commented On Opinion';

        if($this->opinion->user->id==$notifiable->id)
        {
            $notification_text='Commented On Your Opinion';
        }

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
