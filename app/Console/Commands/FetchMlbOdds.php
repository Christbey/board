<?php

// app/Console/Commands/FetchMlbOdds.php

namespace App\Console\Commands;

class FetchMlbOdds extends FetchOddsCommand
{
    protected $signature = 'fetch:mlb-odds';
    protected $description = 'Fetch the latest MLB odds from the API';

    protected function getSport()
    {
        return 'mlb';
    }
}
