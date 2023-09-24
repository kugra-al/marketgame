<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\City;

class CityItem extends Model
{
    use HasFactory;
    protected $table = 'cities_items';
    public $timestamps = false;

    public function player()
    {
    	return $this->city->player();
    }

    public function item()
    {
    	return $this->belongsTo(Item::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
