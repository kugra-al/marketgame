<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Game;

class GameTest extends TestCase
{
    public function test_tick_update()
    {
        $game = Game::find(1);
        if (!$game)
            $game = new Game();
        $game->processTick();
    }
}
