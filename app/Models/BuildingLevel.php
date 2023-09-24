<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BuildingLevelCost;
use App\Models\BuildingLevelReward;

class BuildingLevel extends Model
{
    use HasFactory;
    protected $table = 'buildings_levels';

    public function cost()
    {
     	return $this->hasMany(BuildingLevelCost::class)->with('item');
    }

    public function reward()
    {
     	return $this->hasMany(BuildingLevelReward::class)->with('item');
    }

    public function troops()
    {
        return $this->hasMany(BuildingLevelTroop::class);
    }

    public function transports()
    {
        return $this->hasMany(BuildingLevelTransport::class);
    }

    public function recipes()
    {
        return $this->hasMany(BuildingLevelRecipe::class);
    }
}
