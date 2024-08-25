<?php

namespace App\Console\Commands;

use App\Jobs\Nfl\News\ApiNewsJob;
use Illuminate\Console\Command;

class FetchNflNews extends Command
{
    protected $signature = 'nfl:fetch-news 
                            {playerID? : The ID of the player to fetch news for} 
                            {--topNews : Fetch top news} 
                            {--fantasyNews : Fetch fantasy news} 
                            {--recentNews : Fetch recent news} 
                            {--maxItems=10 : Maximum number of news items to fetch}';

    protected $description = 'Fetch NFL news from the NFLStatsService and store in database';

    public function handle()
    {
        $playerID = $this->argument('playerID');
        $topNews = $this->option('topNews');
        $fantasyNews = $this->option('fantasyNews');
        $recentNews = $this->option('recentNews');
        $maxItems = $this->option('maxItems');

        ApiNewsJob::dispatch($playerID, $topNews, $fantasyNews, $recentNews, $maxItems);

        $this->info('The job to fetch NFL news has been dispatched.');
    }
}
