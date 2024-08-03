<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnInjury extends Model
{
    protected $table = 'nfl_espn_injuries';

    protected $fillable = [
        'team_id',
        'athlete_id',
        'injury_id',
        'type',
        'status',
        'date',
        'description',
    ];

    public function team()
    {
        return $this->belongsTo(NflEspnTeam::class, 'team_id', 'team_id');
    }

    public function athlete()
    {
        return $this->belongsTo(NflEspnAthlete::class, 'athlete_id', 'athlete_id');
    }
}
