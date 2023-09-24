<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BuildingLevels;

class Building extends Model
{
    use HasFactory;
    
    public function levels()
    {
    	return $this->hasMany(BuildingLevel::class,'building_id','id');
    }

    public function costs()
	{
	    return $this->hasManyThrough(BuildingLevelCost::class, BuildingLevel::class);
	}

}
