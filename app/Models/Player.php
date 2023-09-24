<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CityItem;
use App\Models\CityTransport;
use Storage;

class Player extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public static function findPlayerByUserId($user_id)
    {
    	return Player::where('user_id',$user_id)->first();
    }

    public function setupNewPlayer($cityName = null)
    {

        // Setup city
        $city = new City;
        if (!$cityName)
            $cityName = $this->name."'s city";
        $city->name = $cityName;
        $city->player_id = $this->id;
        $city->population = 100;

        $city->level = 1;
        $city->tick_created = Game::getLastTick();
        $city->save();
        $location = new Location;
        $location->x = rand(1,1000);
        $location->y = rand(1,1000);
        $location->city_id = $city->id;
        $location->save();
        $city->addStatus("City created",['type'=>'city','status'=>'complete']);
        // Add items
        $gold = $city->addQtyToItemByName('gold',1000);
        $wood = $city->addQtyToItemByName('wood',100);
        $iron = $city->addQtyToItemByName('iron-ore',100);
        $grain = $city->addQtyToItemByName('grain',100);
        // Add base buildings
        $city->addBuilding(1,true);
        $city->addBuilding(2,true);
        $city->addBuilding(3,true);
        $city->addBuilding(4,true);     
        $city->addBuilding(5,true);    
        $city->addBuilding(6,true);   
	    // Add transports
        $updates = [['transport_id'=>1,'city_id'=>$city->id,'qty'=>10]];
        $city->updateTransports($updates);

        $updates = [['troop_id'=>1,'city_id'=>$city->id,'qty'=>10]];
        $city->updateTroops($updates);
	    return [$city,$gold,$wood];
    }


    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->hasOne(City::class)->with('items');
    }

    public function delete()
    {
        //$this->city()->buildings()->delete();
        $this->city()->delete();
        return parent::delete();
    }

    public function getRandomName()
    {
        $names = Storage::get("public/player-names.json");
        $names = json_decode($names);
        
        return $names[rand(0,sizeof($names)-1)];
    }

}
