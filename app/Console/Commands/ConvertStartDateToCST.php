<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NflEspnEvent;
use Carbon\Carbon;

class ConvertStartDateToCST extends Command
{
    protected $signature = 'convert:espn-event-start-date-cst';
    protected $description = 'Convert NFL ESPN Event start dates to CST';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Fetch all NFL ESPN events
        $events = NflEspnEvent::all();

        foreach ($events as $event) {
            // Check if date is not null
            if ($event->date) {
                // Parse the date assuming it's stored in UTC (adjust if stored in another timezone)
                $originalDate = Carbon::parse($event->date, 'UTC'); // Adjust 'UTC' if your timezone is different

                // Convert the parsed date to CST (America/Chicago)
                $cstDate = $originalDate->setTimezone('America/Chicago');

                // Format the CST date for storage
                $formattedCstDate = $cstDate->format('Y-m-d H:i:s');

                // Update the event record with the CST start date
                $event->date = $formattedCstDate;
                $event->save();

                $this->info("Updated event ID {$event->id} start date to CST: {$formattedCstDate}");
            } else {
                $this->warn("Event ID {$event->id} does not have a start date.");
            }
        }

        $this->info('Start date conversion to CST for NFL ESPN events completed successfully.');
        return 0;
    }
}
