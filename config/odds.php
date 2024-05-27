<?php
return [
    'sports' => [
        'nfl' => [
            'key' => 'americanfootball_nfl',
            'controller' => App\Http\Controllers\NflController::class,
            'job' => App\Jobs\FetchNflOdds::class,
        ],
        'ncaa' => [
            'key' => 'americanfootball_ncaaf',
            'controller' => App\Http\Controllers\NcaaController::class,
            'job' => App\Jobs\FetchNcaaOdds::class,
        ],
        'mlb' => [
            'key' => 'baseball_mlb',
            'controller' => App\Http\Controllers\MlbController::class,
            'job' => App\Jobs\FetchMlbOdds::class,
        ],
        'nba' => [
            'key' => 'basketball_nba',
            'controller' => App\Http\Controllers\NbaController::class,
            'job' => App\Jobs\FetchNbaOdds::class,
        ],
    ],
];
