<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Jobs\FetchNflPlayersJob;

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
