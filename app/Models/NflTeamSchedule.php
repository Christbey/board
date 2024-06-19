<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflTeamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'season_type',
        'away',
        'team_id_home',
        'game_date',
        'game_status',
        'game_week',
        'team_id_away',
        'home',
        'away_result',
        'home_pts',
        'game_time',
        'home_result',
        'away_pts',
    ];

    protected $dates = ['game_date'];

    public function homeTeam()
    {
        return $this->belongsTo(NflTeam::class, 'team_id_home');
    }

    public function awayTeam()
    {
        return $this->belongsTo(NflTeam::class, 'team_id_away');
    }
}
