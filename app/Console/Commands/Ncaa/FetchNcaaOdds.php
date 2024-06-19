<?php
// app/Console/Commands/FetchNcaaOdds.php

namespace App\Console\Commands\Ncaa;

use App\Console\Commands\FetchOddsCommand;

class FetchNcaaOdds extends FetchOddsCommand
{
    protected $signature = 'fetch:ncaa-odds';
    protected $description = 'Fetch the latest NCAA odds from the API';

    protected function getSport(): string
    {
        return 'ncaa';
    }
}

