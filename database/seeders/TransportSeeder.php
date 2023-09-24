<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class TransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('city_transports')->truncate();
        DB::table('transports')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');        
        DB::table('transports')->insert([
        	[
	        	'name'=>'horse',
	        	//'slug'=>'horse',
	        	'speed'=>10,
	        	'capacity'=>10,
                'defense'=>3,
                'attack'=>1,
        	]
        ]);
    }
}
