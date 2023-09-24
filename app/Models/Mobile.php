<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\MobileItem;
use App\Models\MobileTroop;
use App\Models\MobileTransport;

class Mobile extends Model
{
    use HasFactory;

    const STATE_MOVE = 1;
    const STATE_ATTACK = 2;
    const STATE_WAIT = 3;

    const TYPE_CARAVAN = 1;
    const TYPE_ARMY = 2;

    public function transports()
    {
        return $this->hasMany(MobileTransport::class)->with('transport');
    }

    public function troops()
    {
        return $this->hasMany(MobileTroop::class)->with('troop');
    }

    public function locationFrom()
    {
         return $this->belongsTo(Location::class,'location_id_from','id');
    }

    public function locationTo()
    {
         return $this->belongsTo(Location::class,'location_id_to','id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function items()
    {
        return $this->hasMany(MobileItem::class)->with('item');
    }

    public function target()
    {
        return $this->hasOne(Mobile::class,'id','target_mobile_id');
    }

    public function attackers()
    {
        return $this->hasMany(Mobile::class,'target_mobile_id','id');
    }

    // public function attackingArmies()
    // {
    //     return $this->hasMany(Army::class,'target_caravan_id');
    // }

    public function getPercentRouteComplete()
    {
        $percent = ((($this->ticks+($this->ticks-$this->ticks_remaining))/($this->ticks*2))*100);
        if (!$this->returning)
            $percent = $percent-50;
        $percent = round($percent,2);
        return $percent;
    }

    public function getCurrentPosition()
    {
        // Calculate current position on line
        // https://jsfiddle.net/3SY8v/
        $percent = $this->getPercentRouteComplete();
        $xlen = $this->locationFrom->x-$this->locationTo->x;
        $ylen = $this->locationFrom->y-$this->locationTo->y;
        $hlen = sqrt(pow($xlen,2) + pow($ylen,2));
        //$ratio = (($army->locationFrom->calculateDistanceTo($army->locationTo))/$hlen);
        $ratio = ($percent/100)*2;
        if ($this->returning)
            $ratio = ((100-$percent)*2)/100;
        $currentX = $xlen*$ratio;
        $currentY = $ylen*$ratio;
        $currentX = round($this->locationFrom->x-$currentX);
        $currentY = round($this->locationFrom->y-$currentY);

        return ['x'=>$currentX,'y'=>$currentY];
    }

    public function getPositionAtTick($tick, $returning = false)
    {
        $percent = ((($this->ticks+($this->ticks-$tick))/($this->ticks*2))*100);
        if (!$this->returning)
            $percent = $percent-50;
        $percent = round($percent,2);
        $xlen = $this->locationFrom->x-$this->locationTo->x;
        $ylen = $this->locationFrom->y-$this->locationTo->y;
        $hlen = sqrt(pow($xlen,2) + pow($ylen,2));
        //$ratio = (($army->locationFrom->calculateDistanceTo($army->locationTo))/$hlen);
        $ratio = ($percent/100)*2;
        if ($this->returning)
            $ratio = ((100-$percent)*2)/100;
        $currentX = $xlen*$ratio;
        $currentY = $ylen*$ratio;
        $currentX = round($this->locationFrom->x-$currentX);
        $currentY = round($this->locationFrom->y-$currentY);

        return ['x'=>$currentX,'y'=>$currentY];
    }

    public function getAllPositionsOnLine() {
        $guaranteeEndPoint = true;

        if ($this->locationFrom == $this->locationTo)
            return [[]];

        $x1 = $this->locationFrom->x;
        $y1 = $this->locationFrom->y;
        $x2 = $this->locationTo->x;
        $y2 = $this->locationTo->y;
        $xBegin = $x1;
        $yBegin = $y1;
        $xEnd = $x2;
        $yEnd = $y2;
        $dots = array();

        $steep = abs($y2 - $y1) > abs($x2 - $x1);

        if ($steep) {
            $tmp = $x1;
            $x1 = $y1;
            $y1 = $tmp;
            $tmp = $x2;
            $x2 = $y2;
            $y2 = $tmp;
        }

        if ($x1 > $x2) {
            $tmp = $x1;
            $x1 = $x2;
            $x2 = $tmp;
            $tmp = $y1;
            $y1 = $y2;
            $y2 = $tmp;
        }

        $deltax = floor($x2 - $x1);
        $deltay = floor(abs($y2 - $y1));
        $error = 0;
        $deltaerr = $deltay / $deltax;
        $y = $y1;
        $ystep = ($y1 < $y2) ? 1 : -1;

        for ($x = $x1; $x < $x2; $x++) {
            $dots[] = $steep ? array($y, $x) : array($x, $y);       
            $error += $deltaerr;        
            if ($error >= 0.5) {
                $y += $ystep;
                $error -= 1;
            }
        } 

        if ($guaranteeEndPoint) {
        if ((($xEnd - $x) * ($xEnd - $x) + ($yEnd - $y) * ($yEnd - $y)) < (($xBegin - $x) * ($xBegin - $x) + ($yBegin - $y) * ($yBegin - $y))) {
            $dots[] = array($xEnd, $yEnd);
        } else 
            $dots[] = array($xBegin, $yBegin);
        }

        if ($dots[0][0] != $xBegin and $dots[0][1] != $yBegin) {
            return array_reverse($dots);        
        } else {
            return $dots;
        }
    }

    // These are very bad, from city. find a better way to do this
        // Saves a lot of db calls
    public function updateItems($updates)
    {
        $items = [];
        foreach($this->items as $i) {
            $items[$i->item_id] = $i; 
        }
        $newUpdates = [];
        // Check updates for any existing items. Add id and new qty to any existing items.
        foreach($updates as $i=>$u) {

            $oldQty = 0;
            $itemName = "";
            $update = ['qty'=>$u['qty'],'item_id'=>$u['item_id']];
            if (isset($items[$u['item_id']])) {
                $item = $items[$u['item_id']];
                $update['id'] = $item->id;
                $oldQty = $item->qty;
                $update['qty'] = (int)$item->qty+(int)$u['qty'];
                $itemName = $item->item->name;
                if (!isset($u['type']))
                    $u['type'] = $item->type;
                $update['type'] = $u['type'];
            }
            //if (!isset($u['type']))
            //    $update['caravan_id'] = $this->id;
            if (!isset($u['mobile_id']))
                $update['mobile_id'] = $this->id;
            else
                $update['mobile_id'] = $u['mobile_id'];
            $newUpdates[] = $update;


            //$this->addStatus("Item ".$itemName." qty changed from $oldQty to ".$update['qty'],['type'=>'item','status'=>'update']);
        }
        //dd($newUpdates);
        MobileItem::upsert($newUpdates,['id'],['qty']);
        // Upsert doesn't fire updates so manually fire
        //event(new \App\Events\CityUpdate($this));
    }

    public function updateTransports($updates)
    {
        $transports = [];
        foreach($this->transports as $t) {
            $transports[$t->transport_id] = $t; 
        }
        // Check updates for any existing items. Add id and new qty to any existing items.
        foreach($updates as $i=>$u) {
            if (isset($transports[$u['transport_id']])) {
                $updates[$i]['id'] = $transports[$u['transport_id']]->id;
                $updates[$i]['qty'] = (int)$transports[$u['transport_id']]->qty+(int)$updates[$i]['qty'];
            }
            if (!isset($u['mobile_id']))
                $updates[$i]['mobile_id'] = $this->id;
        }
        MobileTransport::upsert($updates,['id'],['qty']);

        foreach($this->transports as $t) {
            if ($t->qty <= 0) {
                $t->delete();
            }
        }
        
    }

    public function moveTarget($coords, $state = null)
    {
        if (!$state)
            $state = $this->STATE_MOVE;

        $oldLocationTo = $this->locationTo;
        $oldLocationFromID = $this->locationFrom->id;
        $location = Location::where([["x",$coords['x']],['y',$coords['y']]])->first();
        if (!$location) {
            $location = new Location;
            $location->x = $coords['x'];
            $location->y = $coords['y'];
            $location->save();
        }
//dd($this->getCurrentPosition());
        $locationFrom = $this->getCurrentPosition();
        $oldLocationFrom = Location::where('id',$oldLocationFromID)->with('city','mobilesFrom','mobilesTo')->first();
        if ($oldLocationFrom->x != $locationFrom['x'] 
            && $oldLocationFrom->y != $locationFrom['y']) {
            $newLocationFrom = Location::where([["x",$locationFrom['x']],['y',$locationFrom['y']]])->first();
            if (!$newLocationFrom) {
                $newLocationFrom = new Location;
                $newLocationFrom->x = $locationFrom['x'];
                $newLocationFrom->y = $locationFrom['y'];
                $newLocationFrom->save();
            }
            $this->location_id_from = $newLocationFrom->id;
            $this->save();
            $oldLocationFrom = Location::where('id',$oldLocationFromID)->with('city','mobilesFrom','mobilesTo')->first();
            if (!$oldLocationFrom->city && !$oldLocationFrom->mobilesFrom->count() && !$oldLocationFrom->mobilesTo->count())
                $oldLocationFrom->delete();

        }
        // Calculate distance and ticks required from transport speed
        $distance = $this->locationFrom->calculateDistanceTo(['x'=>$location->x,'y'=>$location->y]);
        $ticks = ceil($distance/10);
        $this->ticks = $ticks;
        $this->ticks_remaining = $ticks;
        $this->returning = 0;

        $this->location_id_to = $location->id;
        $this->state = $state;
        $this->save();
        $oldLocationTo = Location::where('id',$oldLocationTo->id)->with('city','mobilesFrom','mobilesTo')->first();
        //dd($oldLocation);
        //dd($oldLocation->armiesTo->count());
        if ($oldLocationTo && !$oldLocationTo->city && !$oldLocationTo->mobilesFrom->count() && !$oldLocationTo->mobilesTo->count())
            $oldLocationTo->delete();

        event(new \App\Events\MapUpdate(['mobiles'=>[$this->id]]));
        //else
        //  dd($oldLocation);
    }
}
