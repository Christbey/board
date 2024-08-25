<?php

namespace App\Console\Commands\Espn;

use App\Jobs\Nfl\News\FetchNflNewsJob;
use Exception;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    protected $signature = 'espn:nfl-news';
    protected $description = 'Fetch and store NFL news from ESPN';

    public function handle()
    {
        try {
            // Dispatch the job
            FetchNflNewsJob::dispatch();
            $this->info('FetchNflNewsJob dispatched successfully');
        } catch (Exception $e) {
            $this->error('Failed to dispatch FetchNflNewsJob: ' . $e->getMessage());
        }
    }
}
