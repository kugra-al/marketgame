<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityBuildingCraft extends Model
{
    use HasFactory;

    public function recipe()
    {
        return $this->belongsTo(ItemRecipe::class,'item_recipe_id','id')->with('item','items');
    }

    public function building()
    {
        return $this->belongsTo(CityBuilding::class,'city_building_id','id')->with('city');
    }

    public function delete()
    {
        $this->building->current_workers += $this->workers;
        $this->building->save();
        parent::delete();
    }
}
