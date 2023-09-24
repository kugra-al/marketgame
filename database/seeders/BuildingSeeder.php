<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\Item;
use App\Models\ItemRecipe;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildings = [
            'gold mine' => [
                0 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>50],['item_slug'=>'wood','qty'=>5],['item_slug'=>'iron-ore','qty'=>5]],
                    'reward'=>[],
                    'ticks'=>1,
                    'workers'=>10,
                    'recipes'=>[['item_slug'=>'gold-ingot']]
                ],
                1 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>100],['item_slug'=>'wood','qty'=>10],['item_slug'=>'iron-ore','qty'=>10]],
                    'reward'=>[['item_slug'=>'gold', 'qty'=>10]],
                    'ticks'=>1,
                    'workers'=>10,
                    'recipes'=>[['item_slug'=>'gold-ingot']]
                ],
                2 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>200],['item_slug'=>'wood','qty'=>20],['item_slug'=>'iron-ore','qty'=>20]],
                    'reward'=>[['item_slug'=>'gold', 'qty'=>20]],
                    'ticks'=>3,
                    'workers'=>20,
                    'recipes'=>[['item_slug'=>'gold-ingot']]
                ],
                3 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>300],['item_slug'=>'wood','qty'=>30],['item_slug'=>'iron-ore','qty'=>30]],
                    'reward'=>[['item_slug'=>'gold', 'qty'=>30]],
                    'ticks'=>6,
                    'workers'=>40,
                    'recipes'=>[['item_slug'=>'gold-ingot']]
                ],
                4 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>500],['item_slug'=>'wood','qty'=>50],['item_slug'=>'iron-ore','qty'=>50]],
                    'reward'=>[['item_slug'=>'gold', 'qty'=>50]],
                    'ticks'=>12,
                    'workers'=>100,
                    'recipes'=>[['item_slug'=>'gold-ingot']]
                ],
            ],
            'lumbermill' => [
                0 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>50],['item_slug'=>'wood','qty'=>5],['item_slug'=>'iron-ore','qty'=>5]],
                    'reward'=>[],
                    'ticks'=>1,
                    'workers'=>10,
                    'recipes'=>[['item_slug'=>'wooden-plank'],['item_slug'=>'wooden-handle']]
                ],
                1 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>100],['item_slug'=>'wood','qty'=>100],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[['item_slug'=>'wood', 'qty'=>10]],
                    'ticks'=>1,
                    'workers'=>10,
                    'recipes'=>[['item_slug'=>'wooden-plank'],['item_slug'=>'wooden-handle']]
                ],
                2 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>200],['item_slug'=>'wood','qty'=>200],['item_slug'=>'iron-ore','qty'=>200]],
                    'reward'=>[['item_slug'=>'wood', 'qty'=>20]],
                    'ticks'=>3,
                    'workers'=>20,
                    'recipes'=>[['item_slug'=>'wooden-plank'],['item_slug'=>'wooden-handle']]
                ],
                3 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>400],['item_slug'=>'wood','qty'=>400],['item_slug'=>'iron-ore','qty'=>400]],
                    'reward'=>[['item_slug'=>'wood', 'qty'=>40]],
                    'ticks'=>6,
                    'workers'=>40,
                    'recipes'=>[['item_slug'=>'wooden-plank'],['item_slug'=>'wooden-handle']]
                ],
                4 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>1000]],
                    'reward'=>[['item_slug'=>'wood', 'qty'=>100]],
                    'ticks'=>12,
                    'workers'=>100,
                    'recipes'=>[['item_slug'=>'wooden-plank'],['item_slug'=>'wooden-handle']]
                ],
            ],
            'iron mine' => [
                0 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>50],['item_slug'=>'wood','qty'=>5],['item_slug'=>'iron-ore','qty'=>5]],
                    'reward'=>[],
                    'ticks'=>1,
                    'workers'=>10,
                    'recipes'=>[['item_slug'=>'iron-ingot'],['item_slug'=>'iron-pickaxe'],['item_slug'=>'iron-nail']]
                ],
                1 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>100],['item_slug'=>'wood','qty'=>100],['item_slug'=>'iron-ore','qty'=>10]],
                    'reward'=>[['item_slug'=>'iron-ore', 'qty'=>10]],
                    'ticks'=>1,
                    'workers'=>10,
                    'recipes'=>[['item_slug'=>'iron-ingot'],['item_slug'=>'iron-pickaxe'],['item_slug'=>'iron-nail']]
                ],
                2 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>200],['item_slug'=>'wood','qty'=>200],['item_slug'=>'iron-ore','qty'=>20]],
                    'reward'=>[['item_slug'=>'iron-ore', 'qty'=>20]],
                    'ticks'=>3,
                    'workers'=>20
                ],
                3 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>400],['item_slug'=>'wood','qty'=>400],['item_slug'=>'iron-ore','qty'=>40]],
                    'reward'=>[['item_slug'=>'iron-ore', 'qty'=>40]],
                    'ticks'=>6,
                    'workers'=>40
                ],
                4 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[['item_slug'=>'iron-ore', 'qty'=>100]],
                    'ticks'=>12,
                    'workers'=>100
                ],
            ],
            'farm' => [
                0 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>1,
                    'workers'=>10
                ],
                1 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[['item_slug'=>'grain', 'qty'=>10]],
                    'ticks'=>1,
                    'workers'=>10
                ],
                2 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[['item_slug'=>'grain', 'qty'=>20]],
                    'ticks'=>3,
                    'workers'=>20
                ],
                3 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[['item_slug'=>'grain', 'qty'=>30]],
                    'ticks'=>6,
                    'workers'=>40
                ],
                4 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[['item_slug'=>'grain', 'qty'=>40]],
                    'ticks'=>12,
                    'workers'=>100
                ],
            ],
            'stables' => [
                0 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>1,
                    'workers'=>10
                ],
                1 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>1,
                    'workers'=>10,
                    'transports'=>
                    [
                        [
                            'transport_id'=>1,
                            'cost' => [
                                [
                                    'item_id'=>1,
                                    'qty'=>100
                                ]
                            ]
                        ]
                    ] 
                ],
                2 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>3,
                    'workers'=>20
                ],
                3 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>6,
                    'workers'=>40
                ],
                4 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>12,
                    'workers'=>100
                ],
            ],
            'barracks' => [
                0 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>1,
                    'workers'=>10
                ],
                1 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>1,
                    'workers'=>10,
                    'troops'=>
                    [
                        [
                            'troop_id'=>1,
                            'cost' => [
                                [
                                    'item_id'=>1,
                                    'qty'=>100
                                ]
                            ]
                        ]
                    ]  
                ],
                2 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>3,
                    'workers'=>20
                ],
                3 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>6,
                    'workers'=>40
                ],
                4 => [
                    'cost'=>[['item_slug'=>'gold', 'qty'=>1000],['item_slug'=>'wood','qty'=>1000],['item_slug'=>'iron-ore','qty'=>100]],
                    'reward'=>[],
                    'ticks'=>12,
                    'workers'=>100
                ],
            ],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('buildings')->truncate();
        DB::table('buildings_levels')->truncate();
        DB::table('building_level_rewards')->truncate();
        DB::table('buildings_levels_costs')->truncate();
        DB::table('building_level_troops')->truncate();
        DB::table('building_level_troop_costs')->truncate();
        DB::table('building_level_recipes')->truncate();
        DB::table('cities_buildings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $items = Item::pluck('id','slug');
        $recipes = ItemRecipe::pluck('id','item_id');
        //dd($recipes);

        foreach($buildings as $name=>$data) {
            DB::table('buildings')->insert([['name'=>$name]]);
            $buildingId = DB::getPdo()->lastInsertId();
            foreach($data as $level=>$costRewards) {
                DB::table('buildings_levels')->insert([['building_id'=>$buildingId,'level'=>$level,
                    'ticks'=>$costRewards['ticks'],'workers'=>$costRewards['workers']]]);
                $levelId = DB::getPdo()->lastInsertId();
                foreach($costRewards['cost'] as $cost) {
                    //dd($cost);
                    if (!isset($items[$cost['item_slug']]))
                        continue;
                    $cost['item_id'] = $items[$cost['item_slug']];
                    unset($cost['item_slug']);
                    DB::table('buildings_levels_costs')->insert([array_merge($cost,['building_level_id'=>$levelId])]);
                }
                foreach($costRewards['reward'] as $reward) {
                    //dd($reward);
                    if (!isset($items[$reward['item_slug']]))
                        continue;
                    $reward['item_id'] = $items[$reward['item_slug']];
                    unset($reward['item_slug']);
                    DB::table('building_level_rewards')->insert([array_merge($reward,['building_level_id'=>$levelId])]);                    
                }
                if (isset($costRewards['troops'])) {
                    foreach($costRewards['troops'] as $troop) {
                        //dd($troop);
                        DB::table('building_level_troops')->insert([array_merge(['troop_id'=>$troop['troop_id']],['building_level_id'=>$levelId])]); 
                        $levelTroopId = DB::getPdo()->lastInsertId();
                        if (isset($troop['cost'])) {
                            foreach($troop['cost'] as $cost) {
                                //dd($cost);
                                DB::table('building_level_troop_costs')->insert([array_merge($cost,['building_level_troop_id'=>$levelTroopId])]);
                            }
                        }
                    }
                }
                if (isset($costRewards['transports'])) {
                    foreach($costRewards['transports'] as $transport) {
                        //dd($troop);
                        DB::table('building_level_transports')->insert([array_merge(['transport_id'=>$transport['transport_id']],['building_level_id'=>$levelId])]); 
                        $levelTransportId = DB::getPdo()->lastInsertId();
                        if (isset($transport['cost'])) {
                            foreach($transport['cost'] as $cost) {
                                //dd($cost);
                                DB::table('building_level_transport_costs')->insert([array_merge($cost,['building_level_trans_id'=>$levelTransportId])]);
                            }
                        }
                    }
                }
                if (isset($costRewards['recipes'])) {
                    foreach($costRewards['recipes'] as $recipe) {
                        if (!isset($recipe['item_slug']))
                            continue;
                        if (isset($items[$recipe['item_slug']]) && isset($recipes[$items[$recipe['item_slug']]])) {
                            $recipe['recipe_id'] = $recipes[$items[$recipe['item_slug']]];
                            unset($recipe['item_slug']);
                            DB::table('building_level_recipes')->insert([array_merge($recipe,['building_level_id'=>$levelId])]); 
                        }
                    }
                }
            }
        }

    }
}
