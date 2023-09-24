<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Troop;

class MobileTroop extends Model
{
    use HasFactory;

    public function troop()
    {
        return $this->belongsTo(Troop::class);
    }
}
