<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRecipe extends Model
{
    use HasFactory;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function items()
    {
        return $this->hasMany(ItemRecipeItem::class)->with('item');
    }
}
