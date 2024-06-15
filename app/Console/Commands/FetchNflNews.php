<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Models\NflNews;

class FetchNflNews extends Command
{
    protected $signature = 'nfl:fetch-news {--playerID=} {--topNews} {--fantasyNews} {--recentNews} {--maxItems=10}';
    protected $description = 'Fetch NFL news and store it in the database';
    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle(): void
    {
        $playerID = $this->option('playerID');
        $topNews = (bool)$this->option('topNews');
        $fantasyNews = (bool)$this->option('fantasyNews');
        $recentNews = (bool)$this->option('recentNews');
        $maxItems = $this->option('maxItems');

        $this->info('Fetching NFL news...');

        $response = $this->nflStatsService->getNFLNews($playerID, $topNews, $fantasyNews, $recentNews, $maxItems);
        $newsItems = $response['body'] ?? [];

        if (empty($newsItems)) {
            $this->error('No news data found.');
            return;
        }

        foreach ($newsItems as $newsItem) {
            if (isset($newsItem['link'], $newsItem['title'])) {
                NflNews::updateOrCreate(
                    ['link' => $newsItem['link']],
                    ['title' => $newsItem['title']]
                );
                $this->info('News stored: ' . $newsItem['title']);
            }
        }

        $this->info('NFL news fetched and stored successfully.');
    }
}
