<?php

// app/Console/Commands/FetchNflOdds.php

namespace App\Console\Commands;

class FetchNflOdds extends FetchOddsCommand
{
    protected $signature = 'fetch:nfl-odds';
    protected $description = 'Fetch the latest NFL odds from the API';

    protected function getSport(): string
    {
        return 'nfl';
    }
}
