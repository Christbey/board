<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlbTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
        'conference',
        'division',
        'team_mascot',
        'primary_color',
        'secondary_color',
        'location',
        'stadium',
        'city',
        'state',
    ];

    /**
     * Get the odds where the team is the home team.
     */
    public function homeOdds()
    {
        return $this->hasMany(Odds::class, 'home_team_id');
    }

    /**
     * Get the odds where the team is the away team.
     */
    public function awayOdds()
    {
        return $this->hasMany(Odds::class, 'away_team_id');
    }
}
