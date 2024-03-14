<?php

namespace App\Notifications\Frontend;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Model\Post;
use App\Model\User;
use App\Http\Helpers\FcmMessage;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;



class PostCreated extends Notification implements ShouldQueue
{
    use Queueable;
    protected $post;
    protected $sender;
    protected $fcm_tokens;

    public function __construct(Post $post,User $sender,$fcm_tokens)
    {
        $this->post=$post;
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
        $notification_text='Published New Article : '.$this->post->title;
        return [
            'event'=>'ARTICLE_PUBLISHED',
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
            'body'         => 'Published New Article : '.$this->post->title,
            'image'        =>  $this->post->coverimage,
            'click_action' => 'ARTICLE'
        ])->data([
            'event' => 'ARTICLE_PUBLISHED',
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
        $notification_text='Published New Article';

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
