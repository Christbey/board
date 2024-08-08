<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballPlayWP extends Model
{
    use HasFactory;

    protected $table = 'college_football_play_wp';

    protected $fillable = [
        'game_id',
        'play_id',
        'play_text',
        'home_id',
        'home',
        'away_id',
        'away',
        'spread',
        'home_ball',
        'home_score',
        'away_score',
        'time_remaining',
        'yard_line',
        'down',
        'distance',
        'home_win_prob',
        'play_number',
    ];
}
