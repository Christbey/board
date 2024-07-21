<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Jobs\FetchNflNewsJob;

class FetchNflNews extends Command
{
    protected $signature = 'nfl:fetch-news {--playerID=} {--topNews} {--fantasyNews} {--recentNews} {--maxItems=10}';
    protected $description = 'Fetch NFL news and store it in the database';

    public function handle(): void
    {
        $playerID = $this->option('playerID');
        $topNews = (bool)$this->option('topNews');
        $fantasyNews = (bool)$this->option('fantasyNews');
        $recentNews = (bool)$this->option('recentNews');
        $maxItems = $this->option('maxItems');

        $this->info('Hunting NFL Headlines...');

        FetchNflNewsJob::dispatch($playerID, $topNews, $fantasyNews, $recentNews, $maxItems);
    }
}
