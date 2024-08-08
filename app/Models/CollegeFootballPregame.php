<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballPregame extends Model
{
    use HasFactory;

    protected $table = 'college_football_pregame';

    protected $fillable = [
        'season',
        'season_type',
        'week',
        'game_id',
        'home_team',
        'away_team',
        'spread',
        'home_win_prob',
    ];
}
