<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\City;
use App\Models\Player;


class CityBuilding extends Model
{
    use HasFactory;
    protected $table = "cities_buildings";

    // Get city building is in
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Get type of building
    public function building()
    {
    	return $this->belongsTo(Building::class);
    }

    // Get the current level
    public function level()
    {
    	return $this->hasOne(BuildingLevel::class,'id','building_level_id');
    }

    // Get all possible levels
    public function levels()
    {
    	return $this->hasMany(BuildingLevel::class,'building_id','building_id');
    }

    // Get cost for this level
    public function cost()
    {
    	return $this->hasMany(BuildingLevelCost::class,'building_level_id','building_level_id')->whereNotNull('item_id')->with('item');
    }

    // Get rewards for this level
    public function reward()
    {
        return $this->hasMany(BuildingLevelReward::class,'building_level_id','building_level_id')->with('item');
    }

    public function crafts()
    {
        return $this->hasMany(CityBuildingCraft::class)->with('recipe');
    }

    // Rewards after workers added
    public function getRewards()
    {
        $rewards = $this->reward;
        $workers = $this->current_workers;
        foreach($rewards as $r) {
            $r->qty = $workers;
        }
        return $this->reward;
    }

    // Get nextLevel
    public function nextLevel()
    {
        //dd($this->attributes);
        $currentLevel = $this->level->level;
        return $this->hasOne(BuildingLevel::class,'building_id','building_id')->where(
            function($q) use ($currentLevel) {
                $q->where('level',$currentLevel+1);
            }
        );
        // $currentLevel = $this->level;
        // if ($currentLevel) {
        //     $nextLevel = $this->levels->where('level',$currentLevel->level+1)->first();
        //     if ($nextLevel) {
        //         $this->building_level_id = $nextLevel->id;
        //         $nextLevel = $this->hasOne(BuildingLevel::class,'id','building_level_id');
        //         $this->building_level_id = $currentLevel->id;
        //         return $nextLevel;
        //     }
        // }        
        
        //return $nextLevel;

       // return $this->levels()->where('building_id',$this->building_id)->where('level',$this->level->level+1)->with('cost','reward')->first();
    }

    public function startBuildingUpgrade()
    {
        $city = $this->city;
        $nextLevel = $this->nextLevel;
        $costs = $nextLevel->cost;
        $updates = [];
        if (!$costs)
            return;
        foreach($costs as $c) {
            $updates[] = ['item_id'=>$c->item_id,'qty'=>(0-$c->qty),'city_id'=>$this->id];
        }

        $city->updateItems($updates);
        $this->current_workers += $nextLevel->workers;
        $this->upgrade_ticks_remaining = $nextLevel->ticks;
        $this->save();
        $city->addStatus("Building ".$this->building->name." upgrade to level ".$nextLevel->level." started",['type'=>'building','status'=>'start']);
    }

    public function finishBuildingUpgrade()
    {
        //$nextLevel = $this->level->level + 1;
        //$nextLevelModel = $this->levels()->where('building_id',$this->building_id)->where('level',$nextLevel)->first();
        $nextLevel = $this->nextLevel;
        $this->building_level_id = $nextLevel->id;
        $this->current_workers = $nextLevel->workers;
        $this->building_level_id = $nextLevel->id;
        $this->save();
            
        $this->city->addStatus("Building ".$this->building->name." upgraded to level ".$nextLevel->level,['type'=>'building','status'=>'complete']);
    }

}
