<?php

namespace App\Notifications\Frontend;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Model\Thread;
use App\Model\User;
use App\Http\Helpers\FcmMessage;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;


class ThreadLiked extends Notification implements ShouldQueue
{
    use Queueable;
    protected $thread;
    protected $sender;
    protected $fcm_tokens;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Thread $thread,User $sender,$fcm_tokens)
    {
        $this->thread=$thread;
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
        return [
            'event'=>'THREAD_LIKED',
            'sender_id'=> $this->sender->id,
            'data'=>array('thread_id'=>$this->thread->id,'thread_name'=>$this->thread->name),
            'notification'=>array(
                'message'=>'Liked Thread #'.$this->thread->name,
                'action_url'=> route('thread',['name'=>$this->thread->name])
                )
            ];
    }

    public function toFCM($notifiable){
        $message=new FcmMessage();
        $message->to($this->fcm_tokens)->content([
            'title'        => 'OPINED',
            'body'         => 'Liked Thread #'.$this->thread->name,
            'click_action' => 'THREAD'
        ])->data([
            'event' => 'THREAD_LIKED',
            'thread_id'=>$this->thread->id,
            'thread_name'=>$this->thread->name,
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:''
        ])->priority(FcmMessage::PRIORITY_HIGH)->contentAvailable(true);
        return $message;
    }

    public function toWebPush($notifiable, $notification)
    {
        $notification_text='Liked Thread #'.$this->thread->name;

        return (new WebPushMessage)
            ->title($this->sender->name.' '.$notification_text)
            ->icon('https://www.weopined.com/favicon.png')
            ->body('See opinions on thread #'.$this->thread->name)
            ->tag("Opined")
            ->requireInteraction(true)
            ->renotify(true)
            ->data(['goto'=>route('thread',['name'=>$this->thread->name])]);
    }

}
