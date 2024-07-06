<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflPlayByPlay extends Model
{
    use HasFactory;

    protected $table = 'nfl_play_by_play';

    protected $fillable = [
        'game_id',
        'player_id',
        'play',
        'play_period',
        'play_clock',
        'kick_yards',
        'receptions',
        'targets',
        'rec_yds',
        'pass_attempts',
        'pass_yds',
        'pass_completions',
        'rush_yds',
        'carries',
        'down_and_distance',
        'fg_attempts',
        'fg_yds'
    ];

    public function player()
    {
        return $this->belongsTo(NflPlayer::class);
    }

    public function game()
    {
        return $this->belongsTo(NflTeamSchedule::class, 'game_id');
    }
}
