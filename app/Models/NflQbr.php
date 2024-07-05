<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflQbr extends Model
{
    protected $table = 'nfl_qbr';

    use HasFactory;

    protected $fillable = [
        'team_id',
        'player_id',
        'qbr',
        'attempts',
        'completions',
        'game_id',
        'passing_yards',
        'passing_touchdowns',
        'interceptions'
    ];

    /**
     * Get the team that owns the QBR.
     */
    public function team()
    {
        return $this->belongsTo(NflTeam::class, 'team_id');
    }
}
