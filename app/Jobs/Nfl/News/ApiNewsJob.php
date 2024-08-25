<?php

namespace App\Jobs\Nfl\News;

use App\Models\NflEspnNews;
use App\Notifications\EspnNewsDiscordNotification;
use App\Services\NFLStatsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Log;

class ApiNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $playerID;
    protected $topNews;
    protected $fantasyNews;
    protected $recentNews;
    protected $maxItems;

    /**
     * Create a new job instance.
     */
    public function __construct($playerID = null, $topNews = false, $fantasyNews = false, $recentNews = false, $maxItems = 10)
    {
        $this->playerID = $playerID;
        $this->topNews = $topNews;
        $this->fantasyNews = $fantasyNews;
        $this->recentNews = $recentNews;
        $this->maxItems = $maxItems;
    }

    /**
     * Execute the job.
     */
    public function handle(NFLStatsService $nflStatsService)
    {
        $newsResponse = $nflStatsService->getNFLNews($this->playerID, $this->topNews, $this->fantasyNews, $this->recentNews, $this->maxItems);

        if (isset($newsResponse['body']) && is_array($newsResponse['body'])) {
            foreach ($newsResponse['body'] as $item) {
                if (is_array($item)) {
                    $this->storeNewsItem($item);
                }
            }
        }
    }

    protected function storeNewsItem(array $item)
    {
        $headline = $this->extractHeadlineFromUrl($item['link'] ?? '');

        $newsData = [
            'headline' => $headline,
            'description' => $item['title'] ?? null,
            'url' => $item['link'] ?? null,
            'image_url' => null,
            'byline' => $item['source'] ?? null,
            'published' => $item['published'] ?? now(),
            'last_modified' => now(),
            'team_id' => null,
            'athlete_id' => null,
        ];

        try {
            $nflNews = NflEspnNews::create($newsData);

            Notification::route('discord', config('discord.nfl_news_channel'))
                ->notify(new EspnNewsDiscordNotification($nflNews->id));
        } catch (Exception $e) {
            Log::error("Failed to store news: {$newsData['headline']}. Error: " . $e->getMessage());
        }
    }

    protected function extractHeadlineFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        $rawHeadline = end($segments);
        return ucwords(str_replace('-', ' ', $rawHeadline));
    }
}
