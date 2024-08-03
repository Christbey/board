<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnTeamProjection extends Model
{
    protected $table = 'nfl_espn_team_projections';

    protected $fillable = [
        'team_id',
        'chance_to_win_division',
        'projected_wins',
        'projected_losses',
    ];

    public function team()
    {
        return $this->belongsTo(NflEspnTeam::class, 'team_id', 'team_id');
    }
}
