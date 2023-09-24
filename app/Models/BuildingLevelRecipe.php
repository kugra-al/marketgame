<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingLevelRecipe extends Model
{
    use HasFactory;

    public function recipe()
    {
        return $this->belongsTo(ItemRecipe::class);
    }
}
