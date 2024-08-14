<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class FetchCollegeFootballAdvGameStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $week;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($week = null)
    {
        $this->week = $week;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $options = [];

        if ($this->week) {
            $options['week'] = $this->week;
        }

        // Call the command
        Artisan::call('fetch:college-football-adv-game-stats', $options);
    }
}
