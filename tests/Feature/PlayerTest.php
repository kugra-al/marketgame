<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Player;
use App\Models\User;
use App\Models\Bankaccount;

class PlayerTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_broadcast_player_channel()
    {
        $player = Player::find(1);
        $city = $player->city;
        if ($city) {
            $city->addQtyToNamedItem('gold',1);
            event(new \App\Events\PlayerUpdate($player));
            $city->addQtyToNamedItem('gold',-1);
            event(new \App\Events\PlayerUpdate($player));
        }  
    }

    // public function test_delete_player()
    // {
    //     //$player = Player::find(2);
    //     //$player->city->items->delete();
    //     //$player->city()->delete();
    //     //$player->delete();
    // }

    // public function test_setup_new_player()
    // {
    //     $user = new User;
    //     $user->name = "test";
    //     $user->email = "test".rand(0,10000000)."@test.com";
    //     $user->password = "test";
    //     $user->save();
    //     $player = new Player;
    //     $player->name = "test";
    //     $player->user_id = $user->id;
    //     $player->is_npc = true;
    //     $player->save();
    //     if ($player) {
    //         $setup = $player->setupNewPlayer();
    //         $this->assertTrue(sizeof($setup)===2);
    //     }
    //     return $player;

    // }

    // public function test_player_has_bank_account()
    // {
    //     $player = $this->test_setup_new_player();
    //     //$player->setupNewPlayer();
    //     $this->assertTrue($player->bankaccount !== null);
    // }
}
