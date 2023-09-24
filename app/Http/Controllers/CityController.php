<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\CityBuilding;
use App\Models\CityTroop;
use App\Models\CityBuildingCraft;
use App\Models\BuildingLevelTransport;
use App\Models\ItemRecipe;
use App\Models\BuildingLevelCost;
use App\Models\BuildingLevelRecipe;
use App\Models\Building;
use App\Models\BuildingLevelTroop;
use App\Models\Transport;
use App\Models\CityTransport;
use App\Models\Mobile;
use App\Models\MobileTroop;
use App\Models\Location;
use Auth;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::with('buildings','troops','player')->simplePaginate(25);
        return view('city.index',['cities'=>$cities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $city = City::where('id',$id)->with('items','buildings','troops','buildings.level','buildings.reward','buildings.level.reward','buildings.levels.cost','buildings.level.cost.item:id,name','buildings.level.reward.item','buildings.level.troops')->first();

        $buildings = Building::with('levels')->get();
        $transports = Transport::get();
      
        foreach($buildings as $b) {
            $tmp = [];
           
            $b->costs = $tmp;
        }
        //$city->addBuilding(2);
        return view('city.view',['city'=>$city, 'baseBuildings'=>$buildings,'transports'=>$transports]);
    }

    public function getData($id)
    {
        $city = City::where('id',$id)->
            with('items','items.item:id,name,slug','buildings','troops',
                'buildings.level','buildings.reward','buildings.reward.item:id,name,slug','buildings.level.reward',
                'buildings.level.reward.item:id,name,slug','buildings.level.cost',
                'buildings.level.cost.item:id,name,slug','buildings.level.reward.item:id,name,slug','buildings.level.troops',
            )->first();
        return response()->json($city);        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function craftItem(Request $request)
    {
        $building = CityBuilding::with('city')->find($request->get('city_building_id'));
        $recipe = ItemRecipe::find($request->get('item_recipe_id'));
        $workers = $request->get('workers');
        $qty = $request->get('qty');
//        $player = Auth::user()->player;
        
        if (!$building->city->canUserManage(Auth::user()))
            return back()->with('warning',"This isn't your city");

        if ($workers > $building->current_workers)
            return back()->with('warning',"You don't have enough workers");
  
        $craft = new CityBuildingCraft;
        $craft->city_building_id = $building->id;
        $craft->item_recipe_id = $recipe->id;
        $craft->qty = $qty;
        $craft->ticks = $recipe->ticks;
        $craft->ticks_remaining = $recipe->ticks;
        $craft->workers = $workers;
        $craft->save();
        $building->current_workers = $building->current_workers - $craft->workers;
        $building->save();
        return back()->with('success','Started crafting');
    }

    public function deleteCraft(Request $request)
    {
        //dd($request->all());
        $craft = CityBuildingCraft::with('building')->find($request->get('id'));
        //dd($craft);
        if (!$craft->building->city->canUserManage(Auth::user()))
            return back()->with('warning',"This isn't your city");
        $craft->delete();
        return back()->with('success','Craft deleted');
    }

    public function editCraft(Request $request)
    {
        //dd($request->all());
        $craft = CityBuildingCraft::with('building')->find($request->get('craft_id'));
        if (!$craft->building->city->canUserManage(Auth::user()))
            return back()->with('warning',"This isn't your city");
        $craft->workers = $request->get('workers');
        $craft->qty = $request->get('qty');
        //dd($request->all());
        $craft->save();
        $craft->building->current_workers = $craft->building->current_workers - $craft->workers;
        $craft->building->save();
        return back()->with('success','Craft updated');
    }

    public function craftItemForm(Request $request)
    {
        $building = CityBuilding::find($request->get('city_building_id'));
        $recipe = ItemRecipe::find($request->get('item_recipe_id'));
        return response()->json([
            'form'=>view('city.craft.form',['recipe'=>$recipe,'building'=>$building])->render(),
        ]);
    }

    public function buyTroop(Request $request)
    {
        $player = Auth::user()->player;
        $city = $player->city;
        // add checks

        $cityBuilding = CityBuilding::find($request->get('city_building_id'));
        $troop = BuildingLevelTroop::find($request->get('troop_id'));
        if (!$cityBuilding)
            return back()->with("warning","Couldn't find building");
        if (!$troop)
            return back()->with("warning","Couldn't find troop");

        $cost = $troop->cost;
        $checks = $city->canPlayerAfford($player, $cost);
        if (sizeof($checks))
            return back()->with('warning',"You can't afford that. ".implode($checks));

        foreach($cost as $c) {
            $updates[] = ['item_id'=>$c->item_id,'qty'=>(0-$c->qty),'city_id'=>$city->id];
        }
        $city->updateItems($updates);
        $city->updateTroops([['troop_id'=>$troop->troop_id,'qty'=>1]]);
        return back()->with('success','Added troop');
    }

    public function buyTransport(Request $request)
    {
        $player = Auth::user()->player;
        $city = $player->city;
        // add checks

        $cityBuilding = CityBuilding::find($request->get('city_building_id'));
        $transport = BuildingLevelTransport::find($request->get('transport_id'));
        if (!$cityBuilding)
            return back()->with("warning","Couldn't find building");
        if (!$transport)
            return back()->with("warning","Couldn't find transport");

        $cost = $transport->cost;
        $checks = $city->canPlayerAfford($player, $cost);
        if (sizeof($checks))
            return back()->with('warning',"You can't afford that. ".implode($checks));

        foreach($cost as $c) {
            $updates[] = ['item_id'=>$c->item_id,'qty'=>(0-$c->qty),'city_id'=>$city->id];
        }
        $city->updateItems($updates);
        $city->updateTransports([['transport_id'=>$transport->transport_id,'qty'=>1]]);
        return back()->with('success','Added transport');
    }


    public function moveArmy(Request $request)
    {

        $state = Mobile::STATE_MOVE;
        $data = [];
        $army = $request->get('army_id');
        $player = Auth::user()->player;

        if (!$army) {

            $city = $player->city;
            $army = $city->armies;
            if ($city->armies && $city->armies->count()) {
                $army = $city->armies->first();
            } else {
                $army = new Mobile;
                $army->type = Mobile::TYPE_ARMY;
                $army->location_id_from = $city->location->id;
                $army->location_id_to = $city->location->id;
                $army->city_id = $city->id;
                $army->returning = false;
                $army->ticks = 10;
                $army->ticks_remaining = 10;
                $army->save();
               
                foreach($city->troops as $t) {
                    $armyTroop = new MobileTroop;
                    $armyTroop->qty = 10;
                    $armyTroop->mobile_id = $army->id;
                    $armyTroop->troop_id = $t->troop_id;
                    $armyTroop->save();
                    $t->qty = 0;
                    $t->save();
                }
            }
           // $locationTo = []
        } else {
           
            $army = Mobile::find($army);
        }
        $locationTo = ['x'=>$request->get('x'), 'y'=>$request->get('y')];
        

        // Get current army position and distance to target
        $currentArmyPosition = $army->getCurrentPosition();
        $armyLocation = new Location;
        $armyLocation->x = $currentArmyPosition['x'];
        $armyLocation->y = $currentArmyPosition['y'];
        $armyDistanceToTarget = $armyLocation->calculateDistanceTo(['x'=>$request->get('x'),'y'=>$request->get('y')]);

        // debug test for caravans

        if ($request->get('mobile_id')) {
            
            if ($request->get('mobile_id') == $army->id)
                return ['msg'=>"you can't attack yourself"];

            // Get current mobile position and distance to target
            $mobile = Mobile::find($request->get('mobile_id'));
            $currentMobilePosition = $mobile->getCurrentPosition();
            $mobilePosition = new Location;
            $mobilePosition->x = $currentMobilePosition['x'];
            $mobilePosition->y = $currentMobilePosition['y'];
            $mobileDistanceToTarget = $mobilePosition->calculateDistanceTo([
                'x'=>$request->get('x'),
                'y'=>$request->get('y')
            ]);
            $mobileIntercepts = [$mobileDistanceToTarget];

            // If caravan is closer to target, check if returning then get distance to 
            //  target on return trip
            if ($mobileDistanceToTarget < $armyDistanceToTarget) {
                if ($mobile->returning)
                    $data['error'] = 'will not intercept mobile';
                else {
                    $mobileCityToLocation = $mobile->locationTo;
                    $mobileDistanceToLocation = $mobilePosition->calculateDistanceTo([
                        'x'=>$mobileCityToLocation->x,
                        'y'=>$mobileCityToLocation->y
                    ]);
                    $mobileDistanceToTarget = $mobileDistanceToLocation+
                        $mobileCityToLocation->calculateDistanceTo([
                            'x'=>$request->get('x'),
                            'y'=>$request->get('y')
                        ]);
                    $mobileIntercepts[] = $mobileDistanceToTarget;
                    if ($mobileDistanceToTarget < $armyDistanceToTarget)
                        $data['error'] = 'will not intercept mobile';
                }
            }
            if (!isset($data['error'])) {
                $data['msg'] = "Army moving to intercept mobile at ".json_encode($locationTo)." will take ".ceil($armyDistanceToTarget/10)." ticks. Caravan will get there in "
                    .ceil($mobileDistanceToTarget/10)." ticks";
                
                $army->target_mobile_id = $mobile->id;
                //$army->save();
            }
            // this speed
            //$distance = $distance/10;
            //dd($dist);
            $data['coords'] = [$request->get('x'),$request->get('y')];
            $data['intercepts'] = $mobileIntercepts;
            $data['mobile_distance_to_target'] = $mobileDistanceToTarget;
            $data['army_distance_to_target'] = $armyDistanceToTarget;
            $state = Mobile::STATE_ATTACK;
        } else {
            $locationCheck = Location::where([
                ['x',$locationTo['x']],['y',$locationTo['y']]
            ])->first();
            if ($locationCheck && $locationCheck->city && $locationCheck->city->id != $player->city->id) {
                $data['msg'] = "Army moving to attack city at ".json_encode($locationTo)." will take ".ceil($armyDistanceToTarget/10)." ticks";
                $state = Mobile::STATE_ATTACK;
                //$army->clearTargets();
                //$army->target_city_id = $locationCheck->city->id;
                //$army->save();
            }
        }
        if (!isset($data['error'])) {
            $army->moveTarget($locationTo, $state);
            $data['state'] = $state;
            if (!isset($data['msg']))
                $data['msg'] = "Army moving to ".json_encode($locationTo)." will take ".ceil($armyDistanceToTarget/10)." ticks";
        } else {
            $data['msg'] = "Army unable to move to intercept caravan at ".json_encode($locationTo)." \n Army distance to target is ".ceil($armyDistanceToTarget/10)." ticks, caravan distance to target is ".ceil($mobileDistanceToTarget/10)." ticks";
        }
        return $data;
    }

    public function cityUpgrade(Request $request)
    {
        $id = $request->get('id');
        $city = City::find($id)->first();
        if ($city->upgrade_ticks_remaining > 0)
            return redirect()->route('city.view',['id'=>$city->id])->with('warning',"The city is already being upgraded");

        $player = Auth::user()->player;
        if ($player->id != $city->player_id)
            return redirect()->route('city.view',['id'=>$city->id])->with('warning',"This isn't your city");

        $nextLevel = $city->nextLevel;
        if (!$nextLevel)
            return redirect()->route('city.view',['id'=>$city->id])->with('warning',"City is already max level");
        $checks = $city->canPlayerAfford($player, $nextLevel->costs);
        if (sizeof($checks))
            return redirect()->route('city.view',['id'=>$city->id])->with('warning',"You can't afford that. ".implode($checks));

        $city->startUpgrade();

        return redirect()->route('city.view',['id'=>$city->id])->with('success','City upgrade started');
    }

    public function buildingCreate(Request $request)
    {
        $building = Building::find($request->get('id'));
        $city = City::find($request->get('city_id'));

        if (!$building)
            return back()->with('warning','Building not found');

        $player = Auth::user()->player;
        if ($player->id != $city->player_id)
            return back()->with('warning',"This isn't your city");

        $building = $building->with('levels')->first();
        $level = $building->levels()->with('cost')->first();
        $checks = $city->canPlayerAfford($player, $level->cost, $level->workers);
        if (sizeof($checks))
            return back()->with('warning',"You can't afford that. ".implode($checks));

        $city->addBuilding($building->id, false);
        return back()->with('success','Building create started');


    }

    public function buildingUpgrade(Request $request)
    {
        $building = CityBuilding::find($request->get('id'));
        if (!$building)
            return redirect()->route('city.index')->with('warning','Building not found');

        $city = $building->city;
        if (!$city)
            return redirect()->route('city.index')->with('warning',"City for building not found");

        if ($building->upgrade_ticks_remaining > 0)
            return redirect()->route('city.view',['id'=>$city->id])->with('warning',"The building is already being upgraded");

        $player = Auth::user()->player;
        if ($player->id != $city->player_id)
            return redirect()->route('city.view',['id'=>$city->id])->with('warning',"This isn't your city");

        $nextLevel = $building->nextLevel;
        if (!$nextLevel)
            return redirect()->route('city.view',['id'=>$city->id])->with('warning',"Building is already max level");

        $checks = $city->canPlayerAfford($player, $nextLevel->cost, $nextLevel->workers);
        if (sizeof($checks))
            return redirect()->route('city.view',['id'=>$city->id])->with('warning',"You can't afford that. ".implode($checks));
        $building->startBuildingUpgrade();
        return redirect()->route('city.view',['id'=>$city->id])->with('success','Building upgrade started');
    }
}
