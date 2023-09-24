<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class CityLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$levels = [
            1 => [
                'cost'=>[['item_id'=>1, 'qty'=>200],['item_id'=>2, 'qty'=>100],['item_id'=>3,'qty'=>40],['item_id'=>4,'qty'=>200]],
                'ticks'=>1,
                'population'=>100
            ],
            2 => [
                'cost'=>[['item_id'=>1, 'qty'=>300],['item_id'=>2, 'qty'=>150],['item_id'=>3,'qty'=>80],['item_id'=>4,'qty'=>300]],
                'ticks'=>2,
                'population'=>200
            ],
            3 => [
                'cost'=>[['item_id'=>1, 'qty'=>400],['item_id'=>2, 'qty'=>200],['item_id'=>3,'qty'=>120],['item_id'=>4,'qty'=>400]],
                'ticks'=>3,
                'population'=>500
            ]
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        //DB::table('city_levels')->truncate();
        DB::table('city_level_costs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach($levels as $level=>$data) {
            //DB::table('city_levels')->insert([['level'=>$level,'population'=>$data['population'],'ticks'=>$data['ticks']]]);
            //$levelId = DB::getPdo()->lastInsertId();
            foreach($data['cost'] as $cost) {
                DB::table('city_level_costs')->insert([array_merge($cost,['level'=>$level])]);
            }
        }
    }
}
