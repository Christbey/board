<?php
// KEEP THIS FILE IT WORKS
namespace App\Console\Commands\Nfl\Team;

use App\Jobs\FetchNflPlayersJob;
use Illuminate\Console\Command;

class GetNflPlayers extends Command
{
    protected $signature = 'nfl:get-players';
    protected $description = 'Get NFL Player List';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Dispatching job to fetch NFL player list...');
        FetchNflPlayersJob::dispatch();
        $this->info('Job dispatched successfully.');
    }
}
