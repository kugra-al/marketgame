<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transport;

class MarketOrderTransport extends Model
{
    use HasFactory;

    public function transport()
    {
    	return $this->belongsTo(Transport::class);
    }
}
