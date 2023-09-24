<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public function city()
    {
    	return $this->belongsTo(City::class);
    }

    public function mobilesTo()
    {
    	return $this->hasMany(Mobile::class,'location_id_to','id');
    }

    public function mobilesFrom()
    {
    	return $this->hasMany(Mobile::class,'location_id_from','id');
    }

    public function calculateDistanceTo($coords)
    {   
        $lat1 = $this->x;
        $lon1 = $this->y;
        $lat2 = $coords['x'];
        $lon2 = $coords['y'];
        // $theta = $lon1 - $lon2;
        // $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        // $dist = acos($dist);
        // $dist = rad2deg($dist);
        $dist = sqrt( pow(($lon2-$lon1),2) + pow(($lat2-$lat1),2));
        $dist = $dist/5;
        return round($dist,0);
    }
}
