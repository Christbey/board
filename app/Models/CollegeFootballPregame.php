<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballPregame extends Model
{
    use HasFactory;

    protected $table = 'college_football_pregame';

    protected $fillable = [
        'game_id',
        'home_team_id',
        'away_team_id',
        'spread',
        'home_win_prob',
    ];

    // Define relationships to the CollegeFootballTeam model
    public function homeTeam()
    {
        return $this->belongsTo(CollegeFootballTeam::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(CollegeFootballTeam::class, 'away_team_id');
    }
}
