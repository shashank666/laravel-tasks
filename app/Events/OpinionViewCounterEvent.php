<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Model\ShortOpinion;

class OpinionViewCounterEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $post;
    public $ip_address;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ShortOpinion $post,$ip_address)
    {
       $this->post=$post;
       $this->ip_address=$ip_address;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
