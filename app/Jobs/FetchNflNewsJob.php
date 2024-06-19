<?php

namespace App\Jobs;

use App\Models\NflNews;
use App\Services\NFLStatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchNflNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $playerID;
    protected $topNews;
    protected $fantasyNews;
    protected $recentNews;
    protected $maxItems;

    public function __construct($playerID, $topNews, $fantasyNews, $recentNews, $maxItems)
    {
        $this->playerID = $playerID;
        $this->topNews = $topNews;
        $this->fantasyNews = $fantasyNews;
        $this->recentNews = $recentNews;
        $this->maxItems = $maxItems;
    }

    public function handle(NFLStatsService $nflStatsService)
    {
        $response = $nflStatsService->getNFLNews($this->playerID, $this->topNews, $this->fantasyNews, $this->recentNews, $this->maxItems);
        $newsItems = $response['body'] ?? [];

        if (empty($newsItems)) {
            \Log::info('No news data found.');
            return;
        }

        foreach ($newsItems as $newsItem) {
            if (isset($newsItem['link'], $newsItem['title'])) {
                NflNews::updateOrCreate(
                    ['link' => $newsItem['link']],
                    ['title' => $newsItem['title']]
                );
                \Log::info('News stored: ' . $newsItem['title']);
            }
        }

        \Log::info('NFL news fetched and stored successfully.');
    }
}
