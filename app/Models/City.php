<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\City;
use App\Models\CityItem;
use App\Models\CityBuilding;
use App\Models\Building;
use App\Models\CityTransport;
use App\Models\Mobile;
use App\Models\MobileItem;
use App\Models\MobileTransport;
use App\Models\MarketOrderTransport;
use App\Models\CityStatus;
use App\Models\CityTroop;

use DB;
use Cache;
use Log;

class City extends Model
{
    use HasFactory;

    // get player registered to this city
    public function player()
    {
    	return $this->belongsTo(Player::class);
    }

    // get items for this city
    public function items()
    {
    	return $this->hasMany(CityItem::class)->with('item');
    }

    public function transports()
    {
        return $this->hasMany(CityTransport::class)->with('transport');
    }

    public function troops()
    {
        return $this->hasMany(CityTroop::class)->with('troop');
    }

    public function location()
    {
        return $this->hasOne(Location::class);
    }

    public function armies()
    {
        return $this->hasMany(Mobile::class)->where('type',Mobile::TYPE_ARMY);
    }

    // get buildings in this city
    public function buildings()
    {
        return $this->hasMany(CityBuilding::class)->with('reward','level','cost','building');
    }

    public static function getEloquentSqlWithBindings($query)
    {
        return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
            $binding = addslashes($binding);
            return is_numeric($binding) ? $binding : "'{$binding}'";
        })->toArray());
    }



    // Get cost for this level
    public function costs()
    {
        return $this->hasMany(CityLevelCost::class,'level','level')->with('item');
    }

    public function getItemByName($name)
    {
    	return $this->items()->with(['item' => function($q) use ($name) {
    		$q->where('slug','=',$name);
    	}])->get()->where('item.name',$name)->first();
    }

    public function getItemsByName($itemNames)
    {
		return $this->items()->with(['item' => function($q) use ($itemNames) {
    		$q->whereIn('slug',$itemNames);
    	}])->get();
    }

    public function caravans()
    {
        return $this->hasMany(Mobile::class)->where('type',Mobile::TYPE_CARAVAN);
    }

    public function statuses()
    {
        return $this->hasMany(CityStatus::class);
    }

    public function startUpgrade()
    {
        $nextLevel = $this->nextLevel;
        $costs = $nextLevel->cost;

        $updates = [];
        if (!$costs)
            return;
        foreach($costs as $c) {
            $updates[] = ['item_id'=>$c->item_id,'qty'=>(0-$c->qty),'city_id'=>$this->id];
        }

        $this->updateItems($updates);
        $this->upgrade_ticks_remaining = $nextLevel->ticks;
        $this->save();
        $this->addStatus("City update started",['type'=>'city','status'=>'start']);
    }

    public function finishUpgrade()
    {
        $nextLevel = $this->level+1;
        $this->level = $nextLevel;
        //$this->population = $nextLevel->population;
        $this->save();
        $this->addStatus("City update ended",['type'=>'city','status'=>'complete']);
    }

    // Saves a lot of db calls
    public function updateItems($updates)
    {
        //dd($updates);
        $items = [];
        foreach($this->items as $i) {
            $items[$i->item_id] = $i; 
        }
        $newUpdates = [];
        // Check updates for any existing items. Add id and new qty to any existing items.
        foreach($updates as $i=>$u) {

            $oldQty = 0;
            $itemName = "";
            $update = ['qty'=>$u['qty'],'item_id'=>$u['item_id']];
            if (isset($items[$u['item_id']])) {
                $item = $items[$u['item_id']];
                $update['id'] = $item->id;
                $oldQty = $item->qty;
                $update['qty'] = (float)$item->qty+(float)$u['qty'];
                $itemName = $item->item->name;
            }
            if (!isset($u['city_id']))
                $update['city_id'] = $this->id;
            else
                $update['city_id'] = $u['city_id'];
            if (!isset($update['id']))
                $update['id'] = null;
            $newUpdates[] = $update;


            $this->addStatus("Item ".$itemName." qty changed from $oldQty to ".$update['qty'],['type'=>'item','status'=>'update']);
        }
        //dd($newUpdates);
        CityItem::upsert($newUpdates,['id'],['qty']);
        // Upsert doesn't fire updates so manually fire
        event(new \App\Events\CityUpdate($this));
    }

    public function updateTransports($updates)
    {
        $transports = [];
        foreach($this->transports as $t) {
            $transports[$t->transport_id] = $t; 
        }
        // Check updates for any existing items. Add id and new qty to any existing items.
        foreach($updates as $i=>$u) {
            if (isset($transports[$u['transport_id']])) {
                $updates[$i]['id'] = $transports[$u['transport_id']]->id;
                $updates[$i]['qty'] = (int)$transports[$u['transport_id']]->qty+(int)$updates[$i]['qty'];
            }
            if (!isset($u['city_id']))
                $updates[$i]['city_id'] = $this->id;
        }
        CityTransport::upsert($updates,['id'],['qty']);
        foreach($updates as $t) {
            $this->addStatus("Transport updated ".$t['transport_id']." ".$t['qty'],['type'=>'transport','status'=>'update']);
        }
        // Upsert doesn't fire updates so manually fire
        event(new \App\Events\CityUpdate($this));
    }

    public function updateTroops($updates)
    {
        $troops = [];
        foreach($this->troops as $t) {
            $troops[$t->troop_id] = $t; 
        }
        // Check updates for any existing items. Add id and new qty to any existing items.
        foreach($updates as $i=>$u) {
            if (isset($troops[$u['troop_id']])) {
                $updates[$i]['id'] = $troops[$u['troop_id']]->id;
                $updates[$i]['qty'] = (int)$troops[$u['troop_id']]->qty+(int)$updates[$i]['qty'];
            }
            if (!isset($u['city_id']))
                $updates[$i]['city_id'] = $this->id;
        }
        CityTroop::upsert($updates,['id'],['qty']);
        foreach($updates as $t) {
            $this->addStatus("Troop updated ".$t['troop_id']." ".$t['qty'],['type'=>'troop','status'=>'update']);
        }
        // Upsert doesn't fire updates so manually fire
        event(new \App\Events\CityUpdate($this));
    }
// rewrite this
    public function addQtyToItemByName($name, $qty)
    {
    	$cityItem = $this->getItemByName($name);
    	$updates = [];
    	if (!$cityItem) {
    		$item = Item::where('slug',$name)->first();
    		$updates[] = ['item_id'=>$item->id,'qty'=>$qty];    
    	} 

        $this->updateItems($updates);
    	
    	return $cityItem;
    }

    public function placeMarketOrder($type, $itemID, $qty, $cost = 0)
    {
        $order = new MarketOrder;
        $order->type = $type;

        $orderItem = new MarketOrderItem;
        $orderItem->item_id = $itemID;
        $orderItem->qty = $qty;
        $items = [$orderItem];

        if (!$cost)
            $cost = $orderItem->item->base_cost;
        $cost = $cost*$qty;

        $transportUpdates = [];

        // For a buy item, remove the gold from the buyers city when order is placed
        if ($order->type == "buy") {
            $goldItem = new MarketOrderItem;
            $goldItem->item_id = 1;
            $goldItem->qty = $cost;
            $items = [['item_id'=>1, 'qty' => (0-$cost)]];
            $checks = $this->canPlayerAfford($this->player,[$goldItem]);
            if (sizeof($checks)) {
                return $checks;
            } else {
                $this->updateItems($items);
            }
            $order->city_id_to = $this->id;

        }

        // For a sell item, remove the items and transports from the sellers city when order is placed
        if ($order->type == "sell") {
            $itemWeight = 0;
            foreach($items as $i) {
                $itemWeight += $i->qty*$i->item->weight;
            }
           // dd($itemWeight);
            $checks = $this->canPlayerAfford($this->player,$items,0,$itemWeight);
            if (sizeof($checks)) {
                return $checks;
                
            } else {

                // Remove items from city 
                $itemHold = [['item_id'=>$orderItem->item_id,'qty'=>(0-$orderItem->qty)]];
                $this->updateItems($itemHold);

                // Remove transports from city 
                $itemWeight = $orderItem->item->weight*$orderItem->qty;
                $enoughTransports = false;
                foreach($this->transports as $transport) {
                    if ($enoughTransports)
                        break;
                    $needed = ceil($itemWeight/$transport->transport->capacity);
                    if ($needed <= $transport->qty) {
                        $transportUpdates[] = ['transport_id'=>$transport->transport_id,'qty'=>(0-$needed)];
                        $enoughTransports = true;
                    } else {
                        $transportUpdates[] = ['transport_id'=>$transport->transport_id,'qty'=>(0-$transport->qty)];
                    }
                }
                $this->updateTransports($transportUpdates);
            }
            $order->city_id_from = $this->id;
        }
        $order->save();

        // If transports create updates for market order here
        if (sizeof($transportUpdates)) {
            foreach($transportUpdates as $i=>$t) {
                $transportUpdates[$i]['market_order_id'] = $order->id;
                $transportUpdates[$i]['qty'] = (0-$t['qty']);
            }
            MarketOrderTransport::insert($transportUpdates);
        }

        $orderItem->market_order_id = $order->id;
        $orderItem->cost = $cost;
        $orderItem->save();

        $this->addStatus($order->type." order placed for ".json_encode($orderItem->toArray()),['type'=>'market','status'=>'start']);
        return [];
    }

    public function acceptMarketOrder($id)
    {
        $order = MarketOrder::find($id);

        if (!$order)
            return ['No order found'];
        $city = null;
        if ($order->type == "sell")
            $city = $order->city_from;
        else
            $city = $order->city_to;
        $itemUpdates = [];

        if ($city->player->id === $this->player->id)
            return ["You can't accept your own orders"];

        $items = $order->items;
        $cityTransports = $city->transports;
        if ($order->type == "buy") {

            $itemWeight = 0;
            $goldCost = 0;
            foreach($items as $i) {
                $itemUpdates[] = ['item_id'=>$i->item_id,'name'=>$i->item->name,'qty'=>(0-$i->qty)];
                $itemWeight += $i->item->weight*$i->qty;
                $goldCost += $i->cost;
            }

            $check = $this->canPlayerAfford($this->player,$items,0,$itemWeight);
            if ($check)
                return ["You can't afford that ".implode(",",$check)];

            // Calculate city transports
            $transportUpdates = [];
            $enoughTransports = false;
            $speed = 0;
            foreach($cityTransports as $transport) {
                if ($enoughTransports)
                    break;
                $transportSpeed = $transport->transport->speed;
                if (!$speed || $transportSpeed < $speed)
                    $speed = $transportSpeed;
                $needed = ceil($itemWeight/$transport->transport->capacity);
                if ($needed <= $transport->qty) {
                    $transportUpdates[] = ['transport_id'=>$transport->transport_id,'qty'=>(0-$needed)];
                    $enoughTransports = true;
                } else {
                    $transportUpdates[] = ['transport_id'=>$transport->transport_id,'qty'=>(0-$transport->qty)];
                }
            }

            // Create caravan
            $mobile = new Mobile;
            $mobile->type = Mobile::TYPE_CARAVAN;
            $mobile->city_id = $city->id;
            $mobile->location_id_from = $this->location->id;
            $mobile->location_id_to = $city->location->id;

            // Calculate distance and ticks required from transport speed
            $distance = $mobile->locationFrom->calculateDistanceTo($mobile->locationTo);
            $ticks = ceil($distance/$speed);
            $mobile->ticks = $ticks;
            $mobile->ticks_remaining = $ticks;

            $mobile->save();

            // Update city items
            $this->updateItems($itemUpdates);


            // Update city transports
            $this->updateTransports($transportUpdates);

            // Create new transports for caravan 
            foreach($transportUpdates as $k=>$update) {
                $mobileTransport = new MobileTransport;
                $mobileTransport->mobile_id = $mobile->id;
                $mobileTransport->transport_id = $update['transport_id'];
                $mobileTransport->qty = (0-$update['qty']);
                $mobileTransport->save();
            }

            // Create new items for caravan
            foreach($itemUpdates as $i) {
                $mobileItem = new MobileItem;
                $mobileItem->item_id = $i['item_id'];
                $mobileItem->mobile_id = $mobile->id;
                $mobileItem->qty = (0-$i['qty']);
                $mobileItem->type = MobileItem::TYPE_SEND;
                $mobileItem->save();
            }

            // Add gold for return
            $goldUpdate = new MobileItem;
            $goldUpdate->item_id = 1;
            $goldUpdate->qty = $goldCost;
            $goldUpdate->type = MobileItem::TYPE_RETURN;
            $goldUpdate->mobile_id = $mobile->id;
            $goldUpdate->save();

            $order->delete();

        } else if ($order->type == "sell") {

            $itemWeight = 0;
            $goldCost = 0;
            foreach($items as $i) {
                $itemUpdates[] = ['item_id'=>$i->item_id,'name'=>$i->item->name,'qty'=>$i->qty];
                $itemWeight += $i->item->weight*$i->qty;
                $goldCost += $i->cost;
            }

            $goldUpdate = new MobileItem;
            $goldUpdate->item_id = 1;
            $goldUpdate->qty = $goldCost;


            $goldUpdates = [$goldUpdate];
            $check = $this->canPlayerAfford($this->player,$goldUpdates,0);
            if ($check)
                return ["You can't afford that ".implode(",",$check)];
            $goldUpdates[0]['qty'] = (0-$goldUpdates[0]['qty']);
            $this->updateItems($goldUpdates);

            $speed = 0;
            foreach($order->transports as $t) {
                $transportSpeed = $t->transport->speed;
                if (!$speed || $transportSpeed < $speed)
                    $speed = $transportSpeed;
            }
            // Create caravan
            $mobile = new Mobile;
            $mobile->city_id = $city->id;
            $mobile->location_id_from = $city->location->id;
            $mobile->location_id_to = $this->location->id;

            // Calculate distance and ticks required from transport speed
            $distance = $mobile->locationFrom->calculateDistanceTo($mobile->locationTo);
            $ticks = ceil($distance/$speed);

            $mobile->ticks = $ticks;
            $mobile->ticks_remaining = $ticks;

            $mobile->save();

            // Add gold items for return
            $goldUpdate->qty = (0-$goldUpdate->qty);
            $goldUpdate->type = MobileItem::TYPE_RETURN;
            $goldUpdate->mobile_id = $mobile->id;
            $goldUpdate->save();



            // Create new transports for caravan 
            foreach($order->transports as $transport) {
                $mobileTransport = new MobileTransport;
                $mobileTransport->mobile_id = $mobile->id;
                $mobileTransport->transport_id = $transport->transport_id;
                $mobileTransport->qty = $transport->qty;
                $mobileTransport->save();
            }

            // Create new items for caravan
            // Items should already be removed from city so no need to do update city items
            foreach($itemUpdates as $i) {
                $mobileItem = new MobileItem;
                $mobileItem->item_id = $i['item_id'];
                $mobileItem->mobile_id = $mobile->id;
                $mobileItem->qty = $i['qty'];
                $mobileItem->type = MobileItem::TYPE_SEND;
                $mobileItem->save();
            }
            $order->delete();
//          dd($items);
        }
        $itemStatus = "";
        foreach($itemUpdates as $i) {
            $itemStatus .= $i['name']." ".$i['qty'];
        }
        $statusMsg = $this->name." accepted the order to ".$order->type." $itemStatus";
        $city->addStatus($statusMsg,['type'=>'market','status'=>'start']);
        $this->addStatus($statusMsg,['type'=>'market','status'=>'start']);
        return [];
    }


    public function addBuilding($building, $forFree = false)
    {
    	$building = Building::find($building);
    	if ($building) {
    		$newBuilding = new CityBuilding;
    		$newBuilding->city_id = $this->id;
    		$newBuilding->building_id = $building->id;
            $buildingLevel = $building->levels->first();
    		$newBuilding->building_level_id = $buildingLevel->id;
            $newBuilding->current_workers = $buildingLevel->workers;
            $costs = $buildingLevel->cost();
            $updates = [];
            if (!$forFree) {
                foreach($costs->get() as $c) {
                    $updates[] = ['item_id'=>$c->item_id,'qty'=>(0-$c->qty),'city_id'=>$this->id];
                }
                $this->updateItems($updates);
                $newBuilding->upgrade_ticks_remaining = $buildingLevel->ticks;
            }
    		$newBuilding->save();

            $this->addStatus("New building ".$building->name." added",['type'=>'building','status'=>'start']);
            if ($forFree) {
                $newBuilding->finishBuildingUpgrade();
            }

    		return $newBuilding;
    	}	

    }

    public function canUserManage($user)
    {
        $player = $user->player;
        if (!$player)
            return false;
        return $this->player->id == $user->player->id;
    }

    public function canPlayerAfford(Player $player, $costs, $workers = 0, $transportCapacity = 0)
    {
        if (!$costs && !$workers)
            return [];
        //dd($costs);
        $items = [];
        foreach($this->items as $i) {
            $items[$i->item_id] = $i; 
        }

        $canAfford = [];
        foreach($costs as $cost) {
            if (isset($items[$cost->item_id])) {
                if ($items[$cost->item_id]->qty < $cost->qty)
                    $canAfford[] = "You need ".($cost->qty-$items[$cost->item_id]->qty)." more {$cost->item->name}";
            } else 
                $canAfford[] = "You need {$cost->qty} {$cost->item->name}";
        }
        if ($workers && ($this->getWorkingPopulation()+$workers > $this->population))
            $canAfford[] = "You need {$workers} more workers";
        if ($transportCapacity) {
            $capacity = 0;
            $transports = $this->transports;
            foreach($transports as $t) {
                $capacity += $t->qty*$t->transport->capacity;
            }

            if ($capacity < $transportCapacity)
                $canAfford[] = "You need ".($transportCapacity-$capacity)." more transport capacity";
        }

        return $canAfford;
    }

    public function getArmy()
    {
        $troops = $this->troops;
        $out = ['attack'=>0,'defense'=>0];
        foreach($troops as $t) {
            $out['attack'] += $t->troop->attack+$t->qty;
            $out['defense'] += $t->troop->attack+$t->qty;
        }
        return $out;
    }

    public function getArmySize()
    {
        $troops = $this->troops;
        $army = 0;
        foreach($troops as $t) {
            $army += $t->qty;
        }
        return $army;
    }

    public function getWorkingPopulation()
    {
        $buildings = $this->buildings;
        $workers = 0;
        foreach($buildings as $b) {
            $workers += $b->current_workers;
        }
        return $workers;
    }

    public function addStatus($msg, $options = ['type'=>'city','status'=>'start'])
    {
        $status = new CityStatus;
        $status->city_id = $this->id;
        $status->msg = $msg;
        $status->type = $options['type'];
        if (isset($options['status']))
            $status->status = $options['status'];
        $status->save();
    }

    public function calculateDistanceTo(City $city)
    {   
        return $this->location->calculateDistanceTo(['x'=>$city->location->x,'y'=>$city->location->y]);
    }


    public function getNearestCity($npc = false)
    {
        $cities = City::with('location')->get();
        $lowest = 0;
        $nearestCity = null;
        foreach($cities as $city) {
            if ($city->id == $this->id)
                continue;
            //if ($npc && $city->player && !$city->player->is_npc) {
                //dd($city->player);
            //    continue;
            //}
            $dist = $this->calculateDistanceTo($city);
            if (!$lowest || $dist < $lowest) {
                $lowest = $dist;
                $nearestCity = $city;
            }
        }
        if ($nearestCity)
            $nearestCity->distance = $lowest;
        return $nearestCity;
    }

    public function getCityAge()
    {
        $currentTick = Game::getLastTick();
        return $currentTick-$this->tick_created;
    }

    public function influence()
    {
        $nearest = $this->getNearestCity(true);
        $influence = 0;
        if ($nearest)
            $influence = $nearest->distance;
        //dd($this);
        $influence = $influence + ($this->population/5);
        $influence += $this->getArmySize();
        $influence += $this->getCityAge();
        if ($influence > 150)
            $influence = 150;
        return round($influence);
    }


    // Gets items needed for buildingLevels
    //  items needed for city levels
    //  minus any items already in the city
    // Should also check for market orders
    public function getCityNeeds()
    {
        $itemsStock = $this->items;
        $itemsNeeded = [];
        foreach($this->buildings as $b) {
            $nextLevel = $b->nextLevel;
            if ($nextLevel) {
                foreach($nextLevel->cost as $c) {
                    if (!$c->item->tradeable)
                        continue;
                    if (!isset($itemsNeeded[$c->item_id])) {
                        $tmp = new CityItem;
                        $tmp->item_id = $c->item_id;
                        $tmp->qty = 0;
                        $tmp->item = $c->item;
                        $itemsNeeded[$c->item_id] = $tmp;
                    }
                    $itemsNeeded[$c->item_id]->qty += $c->qty;
                }
            }
        }
        // $nextLevel = $this->nextLevel;
        // if ($nextLevel) {
        //     foreach($nextLevel->cost as $c) {
        //         if (!isset($itemsNeeded[$c->item_id])) {
        //             if (!$c->item->tradeable)
        //                 continue;
        //             $tmp = new CityItem;
        //             $tmp->item_id = $c->item_id;
        //             $tmp->qty = 0;
        //             $tmp->item = $c->item;
        //             $itemsNeeded[$c->item_id] = $tmp;
        //         }
        //         $itemsNeeded[$c->item_id]['qty'] += $c->qty;
        //     }
        // }
        foreach($itemsStock as $s) {
            if (isset($itemsNeeded[$s->item_id]))
                $itemsNeeded[$s->item_id]['qty'] -= $s->qty;
        }
        $itemsNeeded = collect($itemsNeeded);

        return $itemsNeeded->sortBy('qty');
    }
}
