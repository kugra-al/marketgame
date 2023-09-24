<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TickUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tick;
    public $timestamp;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($tick, $timestamp)
    {
        $this->tick = $tick;
        $this->timestamp = $timestamp;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('game-updates');
    }

    public function broadcastAs()
    {
        return "tick-update";
    }
}
