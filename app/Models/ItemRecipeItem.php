<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRecipeItem extends Model
{
    use HasFactory;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function recipe()
    {
        return $this->hasOne(ItemRecipe::class,'id','item_recipe_id');
    }
}
