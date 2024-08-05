<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnPlayByPlay extends Model
{
    protected $table = 'nfl_espn_play_by_play';

    protected $fillable = [
        'game_id',
        'sequenceNumber',
        'type_id',
        'athlete_id',
        'type_text',
        'type_abbreviation',
        'text',
        'shortText',
        'alternativeText',
        'shortAlternativeText',
        'awayScore',
        'homeScore',
        'period_number',
        'clock_value',
        'clock_displayValue',
        'scoringPlay',
        'scoreValue',
        'modified',
        'team_id',
        'wallclock',
        'drive_id',
        'start_down',
        'start_distance',
        'start_yardLine',
        'start_yardsToEndzone',
        'start_downDistanceText',
        'start_shortDownDistanceText',
        'start_possessionText',
        'start_team_id',
        'end_down',
        'end_distance',
        'end_yardLine',
        'end_yardsToEndzone',
        'end_downDistanceText',
        'end_shortDownDistanceText',
        'end_possessionText',
        'end_team_id',
        'statYardage',
    ];

    protected $casts = [
        'scoringPlay' => 'boolean',
        'modified' => 'datetime',
        'wallclock' => 'datetime',
        'clock_value' => 'float',
    ];

    public function team()
    {
        return $this->belongsTo(NflEspnTeam::class, 'team_id', 'team_id');
    }

    public function startTeam()
    {
        return $this->belongsTo(NflEspnTeam::class, 'start_team_id', 'team_id');
    }

    public function endTeam()
    {
        return $this->belongsTo(NflEspnTeam::class, 'end_team_id', 'team_id');
    }
}
