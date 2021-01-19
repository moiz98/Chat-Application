<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;
    public $participants;
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->group = array_pull($request, 'group');
        $this->participants = array_pull($request, 'participants');
        $this->message = array_pull($request, 'message');
        // dd($this->group,$this->participants,$this->message);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = [];

        foreach ($this->participants as $user) {
            array_push($channels, new PrivateChannel('groupMsg.' . $user));
        }
        return $channels;
    }
    public function broadcastWith()
    {
        $this->message->load('fromContact');
        return [ "group" => $this->group, "message" => $this->message];
    }
}
