<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MarketOrderItem;
use App\Models\City;
use App\Models\MarketOrderTransport;

class MarketOrder extends Model
{
    use HasFactory;

    public function items()
    {
    	return $this->hasMany(MarketOrderItem::class)->with('item');
    }

    public function city_from()
    {
        return $this->belongsTo(City::class,'city_id_from','id')->with('player');
    }

    public function city_to()
    {
        return $this->belongsTo(City::class,'city_id_to','id')->with('player');
    }

    public function transports()
    {
        return $this->hasMany(MarketOrderTransport::class)->with('transport');
    }

    public function returnItems()
    {
    	// Return items to city when order is deleted
    	if ($this->type == "sell") {
    		$city = $this->city_from;
    		$items = $this->items;
    		$updates = [];
    		foreach($items as $i) {
    			$updates[] = ['item_id'=>$i->item_id,'qty'=>$i->qty];
    		}
    		$city->updateItems($updates);
    	} else {
    		// Return gold to city when order is deleted
    		$city = $this->city_to;
    		
			$items = $this->items;
    		$updates = [];
    		foreach($items as $i) {
    			$updates[] = ['item_id'=>1,'qty'=>$i->cost];
    		}

    		$city->updateItems($updates);
    	}
    
    }
   
}
