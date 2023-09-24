<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Transport;

class BuildingLevelReward extends Model
{
    use HasFactory;
    protected $table = 'building_level_rewards';

    public function item()
    {
    	return $this->belongsTo(Item::class);
    }

    public function transport()
    {
    	return $this->belongsTo(Transport::class);
    }

    public function troop()
    {
    	return $this->belongsTo(Troop::class);
    }
}
