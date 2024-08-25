<?php

namespace App\Console\Commands;

use App\Notifications\EspnNewsDiscordNotification;
use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Models\NflEspnNews;
use Illuminate\Support\Facades\Notification;

class FetchNflNews extends Command
{
    protected $signature = 'nfl:fetch-news 
                            {playerID? : The ID of the player to fetch news for} 
                            {--topNews : Fetch top news} 
                            {--fantasyNews : Fetch fantasy news} 
                            {--recentNews : Fetch recent news} 
                            {--maxItems=10 : Maximum number of news items to fetch}';

    protected $description = 'Fetch NFL news from the NFLStatsService and store in database';

    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle()
    {
        $playerID = $this->argument('playerID');
        $topNews = $this->option('topNews');
        $fantasyNews = $this->option('fantasyNews');
        $recentNews = $this->option('recentNews');
        $maxItems = $this->option('maxItems');

        $newsResponse = $this->nflStatsService->getNFLNews($playerID, $topNews, $fantasyNews, $recentNews, $maxItems);

        if (isset($newsResponse['body']) && is_array($newsResponse['body'])) {
            $this->info('Fetched NFL News:');
            foreach ($newsResponse['body'] as $item) {
                if (is_array($item)) {
                    $this->storeNewsItem($item);
                } else {
                    $this->error('Unexpected item format in news array.');
                    $this->line(print_r($item, true)); // Debugging output
                }
            }
        } else {
            $this->error('Failed to fetch NFL news or unexpected response format.');
            $this->line(print_r($newsResponse, true)); // Debugging output
        }

        return 0;
    }

    protected function storeNewsItem(array $item)
    {
        // Example mapping - adjust according to your actual data structure
        $newsData = [
            'headline' => $item['title'] ?? null,
            'description' => $item['summary'] ?? null, // Assuming 'summary' exists; adjust if necessary
            'url' => $item['link'] ?? null,
            'image_url' => null, // Assuming you might have to extract this differently, or add logic
            'byline' => $item['source'] ?? null, // Assuming 'source' is the author, adjust if necessary
            'published' => $item['published'] ?? now(), // Assuming you may need to parse the date
            'last_modified' => now(), // Assuming the current time, adjust as necessary
            'team_id' => null, // Assuming you have a way to map or find this, adjust as necessary
            'athlete_id' => null, // Assuming you have a way to map or find this, adjust as necessary
        ];

        $nflNews = NflEspnNews::create($newsData);
        $this->info("Stored news: {$newsData['headline']}");

        // Trigger the notification
        Notification::route('discord', config('discord.nfl_news_channel'))
            ->notify(new EspnNewsDiscordNotification($nflNews->id));
    }

}
