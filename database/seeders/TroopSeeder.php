<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class TroopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('troops')->truncate();   
        DB::table('city_troops')->truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('troops')->insert([
        	[
	        	'name'=>'soldier',
	        	'slug'=>'soldier',
	        	'attack'=>2,
	        	'defense'=>5,
	        	'ranged'=>0
        	]
        ]);
    }
}
