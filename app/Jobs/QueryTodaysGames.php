<?php

namespace App\Jobs;

use App\Models\NflEspnEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class QueryTodaysGames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $games;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today = now()->toDateString();

        // Fetch all games scheduled for today
        $this->games = NflEspnEvent::with(['homeTeam', 'awayTeam'])
            ->whereDate('date', $today)
            ->get();
    }
}
