<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\City;

class CityTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_city_items()
    {
        $city = City::find(22);
        
        $response->assertTrue($city->items !== null);
    }

    public function test_get_named_city_item()
    {
        //$items = City::find(1)->getNamedItems(['gold','wood']);
    }

    public function test_get_city_buildings()
    {
        $city = City::find(22);
       
    }

    // public function test_add_building_to_city()
    // {
    //     $city = City::find(22);
    //     $city->addBuilding(1); 
    // }

    public function test_city_has_next_level()
    {
        $city = City::where('id',1);
       // dd($city);
        $city = $city->with('nextLevel')->first();
        dd($city);
    }
}
