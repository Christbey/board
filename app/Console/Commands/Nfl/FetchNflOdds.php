<?php

namespace App\Console\Commands\Nfl;

use App\Console\Commands\FetchOddsCommand;

class FetchNflOdds extends FetchOddsCommand
{
    protected $signature = 'fetch:nfl-odds';
    protected $description = 'Fetch the latest NFL odds from the API';

    protected function getSport(): string
    {
        return 'nfl';
    }
}
// app/Console/Commands/FetchNflOdds.php
