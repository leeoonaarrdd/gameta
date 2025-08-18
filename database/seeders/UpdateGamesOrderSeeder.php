<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;

class UpdateGamesOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = Game::orderBy('id')->get();
        
        foreach ($games as $index => $game) {
            $game->update(['order' => $index + 1]);
        }
        
        $this->command->info('Games order updated successfully!');
    }
}
