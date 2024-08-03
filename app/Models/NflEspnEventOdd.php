<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnEventOdd extends Model
{
    protected $table = 'nfl_espn_event_odds';

    protected $fillable = [
        'event_id',
        'competition_id',
        'provider_name',
        'provider_id',
        'details',
        'over_under',
        'spread',
        'over_odds',
        'under_odds',
        'away_team_odds',
        'home_team_odds',
        'links',
        'open_odds',
        'current_odds',
    ];

    protected $casts = [
        'away_team_odds' => 'array',
        'home_team_odds' => 'array',
        'links' => 'array',
        'open_odds' => 'array',
        'current_odds' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(NflEspnEvent::class, 'event_id', 'id');
    }
}
