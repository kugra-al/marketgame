<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\MarketOrder;
use App\Models\MarketOrderItem;
use App\Models\BuildingLevelCost;
use App\Models\Mobile;

class MarketController extends Controller
{
    public function index()
    {
    	$player = Auth::user()->player;
    	$items = $player->city->items;
    	$orders = MarketOrder::with('city_from','city_to','items')->get();
        $caravans = Mobile::where('type',Mobile::TYPE_CARAVAN)->with('locationFrom','locationTo','city','transports','items')->get();
    	return view('market.index',['items'=>$items, 'orders'=>$orders,'caravans'=>$caravans]);
    }

    public function order(Request $request)
    {
    	//dd($request->all());
    	$player = Auth::user()->player;
    	$city = $player->city;

    	$request->validate([
    		'qty' => 'required',
    		'item_id' => 'required',
    		'cost' => 'required',
    		'type' => 'required'
    	]);
    	$type = $request->get('type');
    	$itemID = $request->get('item_id');
    	$qty = $request->get('qty');
    	$cost = $request->get('cost');
    	$errors = $city->placeMarketOrder($type, $itemID, $qty, $cost);
    	if (sizeof($errors))
    		return redirect()->route('market')->with('warning',implode(",", $errors));
    	return redirect()->route('market')->with('success','Order Placed');
    
    	//dd($items);
    }

    public function orderAccept(Request $request, $id)
    {
    	$player = Auth::user()->player;
    	$playerCity = $player->city;

    	$errors = $playerCity->acceptMarketOrder($id);
    	if (sizeof($errors))
    		return redirect()->route('market')->with('warning',implode(",", $errors));
    	return redirect()->route('market')->with('success','Order accepted');
    	
    }

    public function orderDelete(Request $request, $id)
    {
    	$player = Auth::user()->player;
    	$order = MarketOrder::find($id);
    	$playerCity = $player->city;
    	$city = null;
    	if ($order->type == "sell")
    		$city = $order->city_from;
    	else
    		$city = $order->city_to;

    	if ($city->player->id !== $player->id)
    		return redirect()->route('market')->with('warning',"You can only delete your own orders");
    	$order->returnItems();
    	$order->delete();
    	$playerCity->addStatus('Market order deleted',['type'=>'market','status'=>'complete']);
    	return redirect()->route('market')->with('success',"Order deleted");
    }
}
