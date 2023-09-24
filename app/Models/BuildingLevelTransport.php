<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingLevelTransport extends Model
{
    use HasFactory;

    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    public function cost()
    {
        return $this->hasMany(BuildingLevelTransportCost::class,'building_level_trans_id','id');
    }
}
