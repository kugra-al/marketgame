<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\Item;
use App\Models\ItemRecipe;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('items')->truncate();
        DB::table('cities_items')->truncate();
        DB::table('item_recipes')->truncate();
        DB::table('item_recipe_items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');  

        $items = [
            [
                'name'=>'gold',
                'slug'=>'gold',
                'weight'=>0,
                'base_cost'=>100,
                'tradeable'=>false
            ],
            [
                'name'=>'wooden log',
                'slug'=>'wood',
                'weight'=>9,
                'base_cost'=>1,
                'tradeable'=>true
            ],
            [
                'name'=>'iron ore',
                'slug'=>'iron-ore',
                'weight'=>6,
                'base_cost'=>5,
                'tradeable'=>true
            ],
            [
                'name'=>'bag of grain',
                'slug'=>'grain',
                'weight'=>2,
                'base_cost'=>3,
                'tradeable'=>true
            ],
            [
                'name'=>'iron ingot',
                'slug'=>'iron-ingot',
                'weight'=>2,
                'base_cost'=>10,
                'tradeable'=>true,
                'craft'=>[
                    'items'=>[
                        [
                            'slug'=>'iron-ore',
                            'qty'=>0.3,
                        ]
                    ],
                    'ticks'=>3
                ]
            ],
            [
                'name'=>'wooden handle',
                'slug'=>'wooden-handle',
                'weight'=>1,
                'base_cost'=>10,
                'tradeable'=>true,
                'craft'=>[
                    'items'=>[
                        [
                            'slug'=>'wooden-plank',
                            'qty'=>0.3,
                        ]
                    ],
                    'ticks'=>3
                ],
            ],
            [
                'name'=>'iron pickaxe',
                'slug'=>'iron-pickaxe',
                'weight'=>6,
                'base_cost'=>15,
                'tradeable'=>true,
                'craft'=>[
                    'items'=>[
                        [
                            'slug'=>"iron-ingot",
                            'qty'=>1,
                        ],
                        [
                            'slug'=>"wooden-handle",
                            'qty'=>1,
                        ]
                    ],
                    'ticks'=>3
                ]
            ],
            [
                'name'=>'iron nail',
                'slug'=>'iron-nail',
                'weight'=>0.1,
                'base_cost'=>1,
                'tradeable'=>true,
                'craft'=>[
                    'items'=>[
                        [
                            'slug'=>"iron-ingot",
                            'qty'=>0.1,
                        ],
                    ],
                    'ticks'=>1
                ]
            ],
            [
                'name'=>'wooden plank',
                'slug'=>'wooden-plank',
                'weight'=>3,
                'base_cost'=>5,
                'tradeable'=>true,
                'craft'=>[
                    'items'=>[
                        [
                            'slug'=>"wood",
                            'qty'=>0.3,
                        ],
                        [
                            'slug'=>'iron-ore',
                            'qty'=>0.3,
                        ]
                    ],
                    'ticks'=>3
                ]
            ],
            [
                'name'=>'gold ingot',
                'slug'=>'gold-ingot',
                'weight'=>3,
                'base_cost'=>5,
                'tradeable'=>true,
                'craft'=>[
                    'items'=>[
                        [
                            'slug'=>"gold",
                            'qty'=>0.3,
                        ],
                    ],
                    'ticks'=>3
                ]
            ],
        ];

        $itemInserts = [];
        $craftInserts = [];
        foreach($items as $i) {
            
            if (isset($i['craft'])) {
                $craftInserts[$i['slug']] = $i['craft'];
                unset($i['craft']);
            }
            $itemInserts[] = $i;
        }

        DB::table('items')->insert($itemInserts);


        $itemIDs = Item::pluck('id','slug');
        foreach($craftInserts as $slug=>$recipeItems) {
            $itemID = $itemIDs[$slug];
            $r = new ItemRecipe;
            $r->item_id = $itemID;
            $r->ticks = $recipeItems['ticks'];
            $r->save();
            $recipeItemsInsert = [];
            foreach($recipeItems['items'] as $i) {
                
                $i['item_id'] = $itemIDs[$i['slug']];
                $i['item_recipe_id'] = $r->id;
                unset($i['slug']);
                $recipeItemsInsert[] = $i;
            }
            DB::table('item_recipe_items')->insert($recipeItemsInsert);
        }
    }
}
