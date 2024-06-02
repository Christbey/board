<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MlbOddsService;
use App\Jobs\FetchMlbOdds;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FetchMlbOddsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'odds:fetch-mlb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch MLB odds and store them in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mlbOddsService = app(MlbOddsService::class);
        FetchMlbOdds::dispatch($mlbOddsService);

        $this->info('MLB odds fetched and job dispatched successfully.');
        return CommandAlias::SUCCESS;
    }
}
