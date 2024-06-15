<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflPlayerStat extends Model
{
    use HasFactory;

    protected $table = 'nfl_player_stats';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'game_id', 'team_id', 'team_abv', 'player_id', 'player_name',
        'rush_yards', 'carries', 'rush_td', 'receptions', 'rec_td', 'targets',
        'rec_yards', 'games_played', 'total_tackles', 'fumbles_lost', 'def_td',
        'fumbles', 'fumbles_recovered', 'solo_tackles', 'defensive_interceptions',
        'qb_hits', 'tfl', 'pass_deflections', 'sacks'
    ];

    public function player()
    {
        return $this->belongsTo(NFLPlayer::class, 'player_id', 'player_id');
    }
}
