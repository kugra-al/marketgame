<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class MobileItem extends Model
{
    use HasFactory;

    const TYPE_SEND = 1;
    const TYPE_RETURN = 2;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
