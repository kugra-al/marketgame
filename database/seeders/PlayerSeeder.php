<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use App\Models\Player;
use App\Models\User;
use App\Models\MarketOrder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!sizeof(User::all())) {
            DB::table('users')->insert([
                [
                    'name'=>'admin',
                    'email'=>'admin@example.com',
                    'password'=>Hash::make('tester')
                ]
            ]);
            $user = DB::getPdo()->lastInsertId();
            DB::table('players')->insert([
                [
                    'name'=>'player',
                    'is_npc'=>false,
                    'user_id'=>$user
                ],
            ]);
            $player = DB::getPdo()->lastInsertId();
            Player::find($player)->setupNewPlayer();

            $admin = Role::where('name','admin')->first();
            if (!$admin)
                $admin = Role::create(['name' => 'admin']);

            $user = User::find($user);
            $user->assignRole('admin');
            //Player::find($player)->setupNewPlayer();
        } else {
            DB::table('users')->insert([
            	[
    	        	'name'=>Str::random(10),
    	        	'email'=>Str::random(10)."@".Str::random(10).".com",
    	        	'password'=>Hash::make(Str::random(10))
            	],
            ]);
            $user = DB::getPdo()->lastInsertId();
            $player = new Player;
            $name = $player->getRandomName();
            DB::table('players')->insert([
            	[
    	        	'name'=>$name,
    	        	'is_npc'=>true,
    	        	'user_id'=>$user
            	],
            ]);
            $player = DB::getPdo()->lastInsertId();
            $player = Player::find($player);
            $player->setupNewPlayer();
            $city = $player->city;
            $cityItems = $city->items;

            foreach($cityItems as $i) {
                if ($i->item_id == 1)
                    continue;
                foreach(['buy','sell'] as $type) {
                    $cost = $i->item->base_cost;
                    if ($type == "sell")
                        $cost = $cost+1;
                    else {
                        if ($cost > 1)
                            $cost = $cost-1;
                    }
                    $city->placeMarketOrder($type,$i->item_id,rand(10,50),$cost);
                }
            }

            $order = MarketOrder::inRandomOrder()->first();
            $city->acceptMarketOrder($order->id);
        }
    }
}
