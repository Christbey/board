<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'base_elo',
        'season_elo',
        'predictive_elo',
        'power_ranking',
        'sos'
    ];

    /**
     * Get the team that owns the ranking.
     */
    public function team()
    {
        return $this->belongsTo(NflTeam::class, 'team_id');
    }
}
