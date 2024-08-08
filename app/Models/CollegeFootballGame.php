<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballGame extends Model
{
    use HasFactory;

    protected $table = 'college_football_games';

    protected $fillable = [
        'id',
        'season',
        'week',
        'season_type',
        'start_date',
        'start_time_tbd',
        'completed',
        'neutral_site',
        'conference_game',
        'attendance',
        'venue_id',
        'venue',
        'home_id',
        'home_team',
        'home_conference',
        'home_division',
        'home_points',
        'home_line_scores',
        'home_post_win_prob',
        'home_pregame_elo',
        'home_postgame_elo',
        'away_id',
        'away_team',
        'away_conference',
        'away_division',
        'away_points',
        'away_line_scores',
        'away_post_win_prob',
        'away_pregame_elo',
        'away_postgame_elo',
        'excitement_index',
        'highlights',
        'notes',
    ];

    protected $casts = [
        'home_line_scores' => 'array',
        'away_line_scores' => 'array',
        'home_post_win_prob' => 'decimal:2',
        'away_post_win_prob' => 'decimal:2',
        'excitement_index' => 'decimal:2',
    ];
}
