<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class BuildingLevelCost extends Model
{
    use HasFactory;
    protected $table = 'buildings_levels_costs';

    public function item()
    {
    	return $this->belongsTo(Item::class);
    }
}
