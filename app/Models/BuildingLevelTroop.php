<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingLevelTroop extends Model
{
    use HasFactory;

    public function troop()
    {
        return $this->belongsTo(Troop::class);
    }

    public function cost()
    {
        return $this->hasMany(BuildingLevelTroopCost::class);
    }
}
