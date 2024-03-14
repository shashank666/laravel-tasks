<?php

namespace App\Notifications\Frontend;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Model\User;
use App\Model\Post;
use App\Http\Helpers\FcmMessage;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;


class PostLiked extends Notification implements ShouldQueue
{
    use Queueable;
    protected $post;
    protected $sender;
    protected $fcm_tokens;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Post $post,User $sender,$fcm_tokens)
    {
        $this->post=$post;
        $this->sender=$sender;
        $this->fcm_tokens=$fcm_tokens;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database',WebPushChannel::class];
        //,'fcm'
    }


    public function toDatabase($notifiable)
    {
        $notification_text='Liked Article : '.$this->post->title;

        if($this->post->user->id==$notifiable->id)
        {
            $notification_text='Liked Your Article : '.$this->post->title;
        }

        return [
            'event'=>'ARTICLE_LIKED',
            'sender_id'=> $this->sender->id,
            'data'=>array('post_id'=>$this->post->id,'post_title'=>$this->post->title),
            'notification'=>array(
                'message'=>$notification_text,
                'action_url'=> route('blog_post',['slug'=>$this->post->slug])
                )
            ];
    }


     public function toFCM($notifiable){
        $message=new FcmMessage();
        $message->to($this->fcm_tokens)->content([
            'title'        => 'OPINED',
            'body'         => 'Liked Article : '.$this->post->title,
            'image'        =>  $this->post->coverimage,
            'click_action' => 'ARTICLE'
        ])->data([
            'event' => 'ARTICLE_LIKED',
            'post_id'=>$this->post->id,
            'post_title'=>$this->post->title,
            'sender_id'=>$this->sender->id,
            'sender_name'=>$this->sender->name,
            'sender_image'=>$this->sender->image!=null?$this->sender->image:''
        ])->priority(FcmMessage::PRIORITY_HIGH)->contentAvailable(true);
        return $message;
    }

    public function toWebPush($notifiable, $notification)
    {
        $notification_text='Liked Article';
        if($this->post->user->id==$notifiable->id)
        {
            $notification_text='Liked Your Article';
        }

        return (new WebPushMessage)
            ->title($this->sender->name.' '.$notification_text)
            ->icon('https://www.weopined.com/favicon.png')
            ->body($this->post->title)
            ->image($this->post->coverimage)
            ->tag("Opined")
            ->requireInteraction(true)
            ->renotify(true)
            ->data(['goto'=>route('blog_post',['slug'=>$this->post->slug])]);
    }

}
