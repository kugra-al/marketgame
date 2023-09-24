<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Player;
use App\Models\City;

class CityUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $city;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(City $city)
    {
        $items = $city->items()->pluck('qty','item_id');
       
        $this->city = collect([
            'id'=>$city->id,
            'name'=>$city->name,
            'items'=>$items
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
       // dd($this->player);
        return new Channel('cities.'.$this->city['id']);
    }

    public function broadcastAs()
    {
        return "city-update";
    }

}
