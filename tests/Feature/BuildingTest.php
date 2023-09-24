<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\CityBuilding;
use App\Models\Building;

class BuildingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_building_has_next_level()
    {
        $building = CityBuilding::where('id',1)->with('level')->first();
        dump($building->load('nextLevel'));
        dd($building->nextLevel);
       // dd($city);
        $city = $city->with('nextLevel')->first();
        dd($city);
    }
}
