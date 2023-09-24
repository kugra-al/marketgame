<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Mobile;
use App\Models\Game;
use App\Models\Location;

class MapController extends Controller
{
    public function index()
    {
        //Game::newGame();
    	return view('map.index',$this->getMapData());
    }

    public function getMapData()
    {
        $locations = Location::with('city','city.troops','city.location','city.player')->get();
        $tmp = [];
        foreach($locations as $l) {
            if ($l->city) {
                $l->city->influence = $l->city->influence();
            }
            $tmp[] = $l;
        }
        $locations = $tmp;


        $mobiles = Mobile::with('locationFrom','locationTo','items','troops','transports','attackers','target')->get();
        $tmp = [];
        foreach($mobiles as $m) {
            $m->current = $m->getCurrentPosition();
            $positions = $m->getAllPositionsOnLine();
            $newPositions = [];
            $step = $m->ticks;
            $step = sizeof($positions)/$step;
            for($x = 1; $x < $m->ticks;$x++) {
                $i = $x*$step;
                if ($i > sizeof($positions))
                    $i = sizeof($positions)-1;

                $pos = $positions[$i];
                $newPositions[] = $pos;
            }
            if (!$m->returning) {
                $from = $m->locationFrom;
                $target = $m->locationTo;
            } else {
                $from = $m->locationTo;
                $target = $m->locationFrom;
            }
            array_unshift($newPositions,[$from->x,$from->y]);
            array_push($newPositions,[$target->x,$target->y]);
            $m->positions = $newPositions;

        }      

        return ['locations'=>$locations,'mobiles'=>$mobiles];
    }
}
