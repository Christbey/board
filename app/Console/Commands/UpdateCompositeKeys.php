<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;

class UpdateCompositeKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:composite-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update composite keys for NFL team schedules and odds';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating composite keys for NFL team schedules...');

        // Update NflTeamSchedule records
        NflTeamSchedule::chunk(100, function ($schedules) {
            foreach ($schedules as $schedule) {
                $schedule->composite_key = NflTeamSchedule::generateCompositeKey($schedule);
                $schedule->save();
            }
        });

        $this->info('Updating composite keys for NFL odds...');

        // Update NflOdd records
        NflOdds::chunk(100, function ($odds) {
            foreach ($odds as $odd) {
                $odd->composite_key = NflOdds::generateCompositeKey($odd);
                $odd->save();
            }
        });

        $this->info('Composite keys updated successfully.');

        return 0;
    }
}
