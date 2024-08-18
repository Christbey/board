<?php

namespace App\Console\Commands\Espn;

use Illuminate\Console\Command;
use App\Jobs\FetchNflNewsJob;

class FetchNews extends Command
{
    protected $signature = 'espn:nfl-news';
    protected $description = 'Fetch and store NFL news from ESPN';

    public function handle()
    {
        // Dispatch the job
        FetchNflNewsJob::dispatch();

        $this->info('FetchNflNewsJob dispatched successfully');
    }
}
