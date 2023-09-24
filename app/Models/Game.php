<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\City;
use App\Models\Game;
use App\Models\Mobile;
use Artisan;
use DB;
use Str;
use Cache;

class Game extends Model
{
    use HasFactory;

    // Needs rewriting, espically with transport/item updates
    // But it works...
    public function processTick()
    {
        //dd("not working");
        Cache::flush();
    	if (!$this->first_tick_update)
    		$this->first_tick_update = \Carbon\Carbon::now()->toDateTimeString();
     	$this->tick = $this->tick+1;
    	$this->last_tick_update = \Carbon\Carbon::now()->toDateTimeString();
    	$this->save();

    	// Update the generation for the buildings in every city
        $marketOrders = MarketOrder::with('items')->get();
        //dd($orders->where('type','buy'));
    	foreach(City::with('buildings','player','buildings.levels','items','transports','troops')->get() as $city) {
			$itemUpdates = [];
            $transportUpdates = [];
            $troopUpdates = [];
    		foreach($city->buildings as $building) {
    			$rewards = $building->getRewards();
    			//dd($rewards->toArray());
                // Need to check here if this is being created and not add rewards until it's finished
    			foreach($rewards as $r) {	
                    // Update items if any
                    if ($r->item_id) {
        				$itemUpdate = [
        					'item_id'=>$r->item_id, 
        					'qty'=>$r->qty,
        					'city_id'=>$city->id
        				];
        				if (isset($itemUpdates[$r->item_id]))
        					$itemUpdates[$r->item_id]['qty'] = $itemUpdates[$r->item_id]['qty']+$r->qty;
        				else
        					$itemUpdates[$r->item_id] = $itemUpdate;
                    } 
    			}
    			if ($building->upgrade_ticks_remaining) {
    				$building->upgrade_ticks_remaining = $building->upgrade_ticks_remaining-1;
    				$building->save();
    				if (!$building->upgrade_ticks_remaining)
				        $building->finishBuildingUpgrade();
    			}
                // Process any pending item crafts
                if ($building->crafts) {
                    foreach($building->crafts as $craft) {
                        $recipe = $craft->recipe;
                        foreach($recipe->items as $item) {
                            if (isset($itemUpdates[$item->item_id]))
                                $itemUpdates[$item->item_id]['qty'] -= $item->qty;
                            else
                                $itemUpdates[$item->item_id] = ['item_id'=>$item->item_id,'qty'=>(0-$item->qty)];
                        }
                        //dd($itemUpdates);
                        $craft->ticks_remaining -= 1;
                        if ($craft->ticks_remaining <= 0) {
                            if ($craft->ticks_remaining == 0)
                                $craft->ticks_remaining = $craft->ticks;
                            if (isset($itemUpdates[$recipe->item_id]))
                                $itemUpdates[$recipe->item_id]['qty'] += 1;
                            else
                                $itemUpdates[$recipe->item_id] = ['item_id'=>$recipe->item_id,'qty'=>1];

                            if ($craft->qty > 0) {
                                $craft->qty -= 1; 
                            }
                            if ($craft->qty == 0)
                                $craft->delete();
                        }
                        if ($craft)
                            $craft->save();

                        //dd($itemUpdates);
                        //dd($craft);
                    }
                }
    		}
            //dd($itemUpdates);
    		if ($city->upgrade_ticks_remaining) {
    			$city->upgrade_ticks_remaining = $city->upgrade_ticks_remaining-1;
    			$city->save();
    			if (!$city->upgrade_ticks_remaining)
    				$city->finishUpgrade();
    		}
    		if (sizeof($itemUpdates))
    			$city->updateItems($itemUpdates);
            if (sizeof($transportUpdates))
                $city->updateTransports($transportUpdates);
            if (sizeof($troopUpdates))
                $city->updateTroops($troopUpdates);

            // Logic for NPCs to buy/sell items
            // This is messy and super slow
            if ($city->player->is_npc) {
                $needs = $city->getCityNeeds();

                
                foreach($needs as $item) {   
                    //dd($item);        
                    // $orders = MarketOrder::where('type','buy')->with(['items' => function($q) use ($item) {
                    //     return $q->where([['id',$item->item_id], ['qty','<=',$item->qty]]);
                    // }])->get();
                    // dd($orders);
                    if ($item->qty < 0) {
                        $orders = $marketOrders->where('type','buy');
                        $order = false;
                        foreach($orders as $o) {
                            if ($o->city_id == $city->id)
                                continue;
                            foreach($o->items as $i) {
                                if ($i->item_id == $item->item_id && $i->qty <= $item->qty) {
                                    $order = $o;
                                    break;
                                }
                            }
                            if ($order)
                                break;
                        }
                        if ($order)
                            $city->acceptMarketOrder($order->id);
                        else
                            $city->placeMarketOrder("sell",$item->item_id,rand(10,50),$item->item->base_cost-1);
                    } else {
                        $orders = $marketOrders->where('type','sell');
                        $order = false;
                        foreach($orders as $o) {
                            if ($o->city_id == $city->id)
                                continue;
                            foreach($o->items as $i) {
                                if ($i->item_id == $item->item_id && $i->qty <= $item->qty) {
                                    $order = $o;
                                    break;
                                }
                            }
                            if ($order)
                                break;
                        }
                        if ($order)
                            $city->acceptMarketOrder($order->id);
                        else
                            $city->placeMarketOrder("buy",$item->item_id,rand(10,50),$item->item->base_cost-1);
                    }
                   // if
                    //if ($qty < 0)
                }
            }
    	}


        $caravans = Mobile::with('items','transports','locationFrom','locationTo','city')->where('type',Mobile::TYPE_CARAVAN)->get();
        foreach($caravans as $caravan) {
            $caravan->ticks_remaining -= 1;
            if ($caravan->ticks_remaining <= 0) {
                // Caravan delivered goods and is back at home city, so return transports to city_to 
                //  and delete caravan
                if ($caravan->returning) {
                    $transportUpdates = [];
                    foreach($caravan->transports as $t) {
                        $transportUpdates[] = ['transport_id'=>$t->transport_id,'qty'=>$t->qty];
                    }
                    $itemUpdates = [];
                    foreach($caravan->items->where('type',MobileItem::TYPE_RETURN) as $i) {
                        $itemUpdates[] = ['item_id'=>$i->item_id, 'qty'=>$i->qty];
                        $i->delete();
                    }
                    $caravan->locationFrom->city->updateItems($itemUpdates);
                    $caravan->locationTo->city->addStatus("Transports returned home",['type'=>'market','status'=>'complete']);
                    $caravan->locationTo->city->updateTransports($transportUpdates);
                    $caravan->delete();
                    continue;
                } else {
                    // Caravan delivered goods to city_to, so give goods to city_to and return caravan to 
                    //  city from
                    $itemUpdates = [];
                    foreach($caravan->items->where('type',MobileItem::TYPE_SEND) as $i) {
                        $itemUpdates[] = ['item_id'=>$i->item_id, 'qty'=>$i->qty];
                        $i->delete();
                    }
                    $caravan->locationFrom->city->addStatus('Items delivered',['type'=>'market','status'=>'complete']);
                    $caravan->locationTo->city->addStatus('items delivered',['type'=>'market','status'=>'complete']);
                    $caravan->locationTo->city->updateItems($itemUpdates);
                    $caravan->ticks_remaining = $caravan->ticks;
                    $caravan->returning = true;
                }
            }
            $caravan->save();
        }

        // Load mobile again so we can get any changes from the above
        $armies = Mobile::with('items','transports','locationFrom','locationTo','city','target')->where('type',Mobile::TYPE_ARMY)->get();
        foreach($armies as $army) {
            $army->ticks_remaining -= 1;
            if ($army->ticks_remaining <= 0) {
                if ($army->ticks_remaining < 0)
                    $army->ticks_remaining = 0;
                if ($army->returning) {
                    if ($army->locationTo->city == $army->city) {
                        $army->city->addStatus("Army returned home. Troops didn't transfer. Sending out again");
                    } else {
                        $army->city->addStatus("Returned to an unknown city");
                    }
                    $army->returning = false;
                    $army->ticks_remaining = $army->ticks;
               
                } else {
                    //dd($army);
                    switch($army->state) {
                        case Mobile::STATE_ATTACK : $locationTo = $army->locationTo;
                                        // if ($army->targetCity) {
                                        //     $army->city->addStatus("Army attacked ".$locationTo->city->name);
                                        //     $army->locationTo->city->addStatus("Army from ".$army->city->name." attacked you");
                                        //     $army->state = Army::STATE_SEIGE_CITY;
                                        //     $army->save();
                                        // } 

                                        // Check for mobile attacks
                   
                                        if ($army->target) {
                                            $mobile = $army->target;
                                            // Calculate battle result and return army to city after attack if pos is the same
                                            $mobilePos = $mobile->getCurrentPosition();
                                            $armyPos = $army->getCurrentPosition();
                                            //dump($mobilePos);
                                            //dd($armyPos);
                                            if ($mobilePos['x'] == $armyPos['x'] && $mobilePos['y'] == $armyPos['y']) {
                                                $army->city->addStatus("Army attacked a mobile");
                                                Game::calculateBattleResult($army,$mobile);
                                                $army->city->addStatus("Army returning to city");
                                                $army->location_id_from = $army->location_id_to;
                                                $army->location_id_to = $army->city->location->id;
                                                $army->returning = true;
                                                $army->ticks_remaining = $army->ticks;
                                                $army->state = Mobile::STATE_MOVE;
                                                
                                            } else {
                                                // Otherwise we're waiting
                                                $army->city->addStatus("Army waiting to loot a caravan");
                                            }      
                                            //dd('army save');
                                           

                                                                                 
                                        }
                                        
                                        
                                        break;
                        case Mobile::STATE_MOVE   : $army->city->addStatus("Army moved to target");
                                        
                                        break;

                        default       : $army->city->addStatus("Unknown state ".$army->state);
                                        break;
                    }
                    //continue;
                    
                }
            }
            if ($army->ticks_remaining >= 0)
                $army->save();
        }


        event(new \App\Events\MapUpdate());
		event(new \App\Events\TickUpdate($this->tick, \Carbon\Carbon::create($this->last_tick_update)->format('H:i:s')));
    }

    public static function calculateBattleResult(Mobile $attacker, $defender, $skipApplyResults = false, $suppressMessages = false)
    {

        $defenderClass = class_basename($defender);
        $defendingArmy = [];
        $results = [
            'errors'=>[],
            'items'=>[
                'attacker'=>[],
                'defender'=>[]
            ],
            'deaths'=>[
                'attacker'=>[],
                'defender'=>[]
            ],
            'transports'=>[
                'attacker'=>[],
                'defender'=>[]
            ],
            'result'=>[],
            'attacker'=>$attacker,
            'defender'=>$defender
        ];

        $attackingArmyTroops = $attacker->troops;
        $attackingArmyAttackStat = 0;
        $attackingArmyDefenseStat = 0;
        $defendingArmyAttackStat = 0;
        $defendingArmyDefenseStat = 0;

        // Calculate attacking army attack stat
        foreach($attackingArmyTroops as $t) {
            $attackingArmyAttackStat += $t->qty*$t->troop->attack;
            $attackingArmyDefenseStat += $t->qty*$t->troop->defense;
        }


        // Calculate defending army defense stat
        
        $defenderTransports = $defender->transports;
        foreach($defenderTransports as $t) {
            $defendingArmyAttackStat += $t->qty*$t->transport->attack;
            $defendingArmyDefenseStat += $t->qty*$t->transport->defense; 
        }
             //dd($defendingArmyStat);                   
        
        $attackingCity = $attacker->city;
        $defendingCity = $defender->city;

        if (!sizeof($results['errors'])) {
            $battleResult = (100 - (($defendingArmyDefenseStat/$attackingArmyAttackStat)*100)); 
            
            if ($battleResult > 0) {
                
                // Check attackerCapacity and process transports stolen first to increase
                //  capacity
                $attackerCapacity = 0;
                foreach($defenderTransports as $t) {
                    $qtyTaken = ceil(($t->qty/100*$battleResult)/2);
                    if ($qtyTaken <= 0)
                        continue;
                    $attackerCapacity += $qtyTaken*$t->transport->capacity;
                    $results['transports']['attacker'][] = ['transport_id'=>$t->transport_id,'qty'=>$qtyTaken];
                    $results['transports']['defender'][] = ['transport_id'=>$t->transport_id,'qty'=>(0-$qtyTaken)];
                }

                $items = [];
                if ($defender->returning)
                    $items = $defender->items->where('type',MobileItem::TYPE_RETURN);
                else
                    $items = $defender->items->where('type',MobileItem::TYPE_SEND);

                // Loop through items as long as there's still capacity.
                foreach($items as $i) {
                    if ($attackerCapacity <= 0)
                        break;

                    $qtyTaken = ceil($i->qty/100*$battleResult);
                    $qtyCapacity = $qtyTaken*$i->item->weight;
                    if ($attackerCapacity < $qtyCapacity)
                        $qtyTaken = $attackerCapacity;
                    if ($qtyTaken <= 0)
                        continue;

                    $attackerCapacity -= $qtyCapacity;
                    $results['items']['attacker'][] = ['item_id'=>$i->item_id,'qty'=>$qtyTaken];
                    $results['items']['defender'][] = ['item_id'=>$i->item_id,'qty'=>(0-$qtyTaken)];
                }

                if (!$skipApplyResults) {

                    $defender->updateTransports($results['transports']['defender']);

                    // Reload defender here to check the new transports
                    $defender = Mobile::with("transports")->find($defender->id);
                   // dd($defender);
                    $transportCount = 0;
                    foreach($defender->transports as $t) {
                        $transportCount += $t->qty;
                    }
                    // If no transports left delete the caravan
                    if (!$transportCount) {
                        $attacker->target_mobile_id = null;
                        $attacker->save();
                        if (!$suppressMessages) {
                            $attacker->city->addStatus("Army wiped out caravan and got ".json_encode([$results['items']['attacker'],$results['transports']['attacker']]));
                            $defender->city->addStatus("Army wiped out your caravan and got ".json_encode([$results['items']['attacker'],$results['transports']['attacker']]));
                        }
                        $defender->delete();
                    } else 
                        $defender->updateItems($results['items']['defender']);


                    $attacker->updateItems($results['items']['attacker']);
                    $attacker->updateTransports($results['transports']['attacker']);
                }
            } else {
                
                dd([$defendingArmyAttackStat,$attackingArmyDefenseStat]);
            }                
            
            // Items/transports should already be removed from defenders
            // Add new items and transports to attacking army. 
            if (!$suppressMessages) {
                $attackingCity->addStatus("Army attacked caravan and got ".json_encode([$results['items']['attacker'],$results['transports']['attacker']]));
                $defendingCity->addStatus("Army attacked caravan and got ".json_encode([$results['items']['attacker'],$results['transports']['attacker']]));
            }

            $results['result'] = [
                'attack'=>$attackingArmyAttackStat,
                'defense'=>$defendingArmyDefenseStat,
                'result'=>$battleResult
            ];
            if ($skipApplyResults)
                $results['result']['simulated'] = true;

        }
        return $results;
    }

    public static function getUnusedLocation()
    {
        $locations = Location::with('city')->get();
//         foreach($locations as $l) {

//         }
// dd($locations);
        $location = ['x'=>1,'y'=>1,'data'=>'not working'];
        return $location;
    }


    public static function getLastTick()
    {
        return Game::getLastTickDataForActiveGame()['tick'];
    }

    public static function getLastTickDataForActiveGame()
    {
    	
    	return Cache::rememberForever('last-tick',function() {
            $game = Game::find(1);
            if (!$game) 
                $game = new Game;
            return collect(['tick'=>$game->tick,'timestamp'=>\Carbon\Carbon::create($game->last_tick_update)->format('H:i:s')]);
        });
    }

    public static function newGame()
    {
        Cache::flush();
    	Artisan::call('migrate:fresh');
    	$game = new Game;
    	$game->tick = 1;
    	$game->save();
    	Artisan::call('db:seed --class=ItemSeeder');
        Artisan::call('db:seed --class=TransportSeeder');
        Artisan::call('db:seed --class=TroopSeeder');
    	Artisan::call('db:seed --class=BuildingSeeder');
    	Artisan::call('db:seed --class=CityLevelSeeder');


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('market_order_items')->truncate();
        DB::table('market_orders')->truncate();
        DB::table('mobiles')->truncate();
        DB::table('mobile_transports')->truncate();
        DB::table('mobile_items')->truncate();
        DB::table('mobile_troops')->truncate();
        DB::table('city_statuses')->truncate();
        DB::table('locations')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');  

        Artisan::call('db:seed --class=PlayerSeeder');
    }

    public static function plural($str,$amount=0)
    {
        $str = Str::plural($str);
        if ($amount > 0 && $str == "bag of grains")
            $str = "bags of grain";
        return $str;
    }
}
