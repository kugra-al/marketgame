<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Location;
use App\Models\Mobile;

class MapUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $locations;
    public $mobiles;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        if (isset($data['locations']))
            $this->locations = $data['locations'];
        if (isset($data['mobiles']))
            $this->mobiles = $data['mobiles'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('map-updates');
    }

    public function broadcastAs()
    {
        return "map-update";
    }
}
