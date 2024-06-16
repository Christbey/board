<?php

// app/Console/Commands/FetchScoresCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchScoresJob;

class FetchScoresCommand extends Command
{
    protected $signature = 'scores:fetch {sport}';
    protected $description = 'Fetch the latest scores from the API for a given sport';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $sport = $this->argument('sport');
        FetchScoresJob::dispatch($sport);
        $this->info("FetchScoresJob for {$sport} dispatched.");
    }
}
