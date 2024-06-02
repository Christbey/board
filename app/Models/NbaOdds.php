<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NbaOdds extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'sport_title',
        'sport_key',
        'home_team_id',
        'away_team_id',
        'h2h_home_price',
        'h2h_away_price',
        'is_live',
        'spread_home_point',
        'spread_away_point',
        'spread_home_price',
        'spread_away_price',
        'total_over_point',
        'total_under_point',
        'total_over_price',
        'total_under_price',
        'commence_time',
        'bookmaker_key',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(NbaTeam::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(NbaTeam::class, 'away_team_id');
    }
}
