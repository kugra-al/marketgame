<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public function recipes()
    {
        return $this->hasMany(ItemRecipe::class);
    }

    public function recipesUsedWith()
    {
        return $this->hasMany(ItemRecipeItem::class);
    }
}
