<?php

// app/Console/Commands/FetchNbaOdds.php

namespace App\Console\Commands;

class FetchNbaOdds extends FetchOddsCommand
{
    protected $signature = 'fetch:nba-odds';
    protected $description = 'Fetch the latest NBA odds from the API';

    protected function getSport(): string
    {
        return 'nba';
    }
}
