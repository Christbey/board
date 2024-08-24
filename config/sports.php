<?php
return [
    'mlb' => [
        'sport_key' => 'baseball_mlb',
        'team_model' => App\Models\MlbTeam::class,
        'odds_model' => App\Models\MlbOdds::class,
        'history_model' => App\Models\MlbOddsHistory::class,
    ],
    'nba' => [
        'sport_key' => 'basketball_nba',
        'team_model' => App\Models\NbaTeam::class,
        'odds_model' => App\Models\NbaOdds::class,
        'history_model' => App\Models\NbaOddsHistory::class,
    ],
    'nfl' => [
        'sport_key' => 'americanfootball_nfl',
        'team_model' => App\Models\NflTeam::class,
        'odds_model' => App\Models\NflOdds::class,
        'history_model' => App\Models\NflOddsHistory::class,
    ],
    'ncaa' => [
        'sport_key' => 'americanfootball_ncaaf',
        'team_model' => App\Models\NcaaTeam::class,
        'odds_model' => App\Models\NcaaOdds::class,
        'history_model' => App\Models\NcaaOddsHistory::class,
    ],
];
