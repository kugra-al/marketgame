<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityItemRecipe extends Model
{
    use HasFactory;

    public function recipe()
    {
        return $this->hasOne(ItemRecipe::class,'id','recipe_id')->with('item');
    }
}
