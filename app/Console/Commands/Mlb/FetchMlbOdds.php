<?php

// app/Console/Commands/FetchMlbOdds.php

namespace App\Console\Commands\Mlb;

use App\Console\Commands\FetchOddsCommand;

class FetchMlbOdds extends FetchOddsCommand
{
    protected $signature = 'fetch:mlb-odds';
    protected $description = 'Fetch the latest MLB odds from the API';

    protected function getSport(): string
    {
        return 'mlb';
    }
}
