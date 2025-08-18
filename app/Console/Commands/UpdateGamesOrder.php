<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;

class UpdateGamesOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:update-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order for all games that do not have order value';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating games order...');

        // Get all games ordered by ID
        $games = Game::orderBy('id')->get();
        
        $updatedCount = 0;
        
        foreach ($games as $index => $game) {
            $newOrder = $index + 1;
            
            // Update if order is null, 0, or different from expected
            if ($game->order === null || $game->order === 0 || $game->order !== $newOrder) {
                $game->update(['order' => $newOrder]);
                $updatedCount++;
                $this->line("Updated game '{$game->name}' order to {$newOrder}");
            }
        }

        $this->info("Successfully updated {$updatedCount} games order!");
        
        return 0;
    }
}
