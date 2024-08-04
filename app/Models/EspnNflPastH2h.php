<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspnNflPastH2h extends Model
{
    use HasFactory;

    protected $table = 'nfl_espn_past_h2h';

    protected $fillable = [
        'event_id',
        'home_team_id',
        'away_team_id',
        'spread',
        'over_odds',
        'under_odds',
        'away_team_money_line_odds',
        'away_team_spread_odds',
        'away_team_spread_winner',
        'away_team_money_line_winner',
        'home_team_money_line_odds',
        'home_team_spread_odds',
        'home_team_spread_winner',
        'home_team_money_line_winner',
        'line_date',
        'total_line',
        'total_result',
        'moneyline_winner',
        'spread_winner',
    ];
}
