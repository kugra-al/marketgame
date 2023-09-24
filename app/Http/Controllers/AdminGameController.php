<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Player;
use App\Models\Item;
use App\Models\Mobile;
use Artisan;

class AdminGameController extends Controller
{
    public function index()
    {
        $players = Player::pluck('name','id');
        $items = Item::pluck('name','id');

        $caravanCollection = Mobile::where('type',Mobile::TYPE_CARAVAN)->with('city')->get();
        $caravans = [];
        foreach($caravanCollection as $i) {
            $caravans[$i->id] = $i->city->name;
        }
        $armyCollection = Mobile::where('type',Mobile::TYPE_ARMY)->with('city')->get();
        $armies = [];
        foreach($armyCollection as $i) {
            $armies[$i->id] = $i->city->name;
        }
    	return view('admin.game.index',['players'=>$players,'items'=>$items,'armies'=>$armies,'caravans'=>$caravans]);
    }

    public function processTick()
    {
    	$game = Game::find(1);
    	if (!$game)
    		$game = new Game;
    	$game->processTick();
    	return view('admin.game.index')->with('info','Game updated to tick '.$game->tick);
    }

    public function post(Request $request)
    {
    	if ($request->get('processTick'))
    		return $this->processTick();
    	if ($request->get('addGold') && $request->get("player_id") && $request->get("amount") && $request->get('item_id')) {
    		$player = Player::find($request->get("player_id"));
            $item = Item::find($request->get('item_id'));
    		$city = $player->city;
    		$account = $city->updateItems([['item_id'=>(int)$request->get('item_id'),'qty'=>(int)$request->get("amount")]]);
    		
	        $player->bankaccount = $account;
	        //event(new \App\Events\CityUpdate($player));
	        return redirect()->route('admin.game')->with('success','Gold added');
    	}
        if ($request->get('addNpcPlayer')) {
            $qty = $request->get('qty');
            if (!$qty)
                $qty = 1;
            for($x = 0; $x < $qty; $x++) {
                Artisan::call('db:seed --class=PlayerSeeder');
            }
            return redirect()->route('admin.game')->with('success',"$qty NPC Player/s added");
        }
        if ($request->get('resetGame')) {
            Game::newGame();
            return redirect()->route('admin.game')->with('success','Game reset');
        }

        if ($request->get('testBattle')) {
            $army = $request->get('army_id');
            $caravan = $request->get('caravan_id');

            if (!$army || !$caravan) {
                return redirect()->route('admin.game')->with('warning','No army or caravan');
            }
            $army = Mobile::find($army);
            $caravan = Mobile::find($caravan);
            if (!$army || !$caravan) {
                return redirect()->route('admin.game')->with('warning','No army or caravan');
            }
            // dd([
            //     $caravan->getCurrentPosition(),$army->getCurrentPosition()
            // ]);
            
            $skipApplyResults = $request->get("skipApplyResults");
            
            $results = Game::calculateBattleResult($army,$caravan,$skipApplyResults);
            dd($results);
        }

        if ($request->get('unusedLocation')) {
            $location = json_encode(Game::getUnusedLocation());
            return redirect()->route('admin.game')->with('success','Location: '.$location);
        }
    	return redirect()->route('admin.game')->with('warning','Unknown post method');
    }
}
