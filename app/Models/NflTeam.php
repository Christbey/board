<?php

// app/Models/NflTeam.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflTeam extends Model
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

    public function rankings()
    {
        return $this->hasMany(NflRanking::class, 'team_id');
    }

    public function qbrs()
    {
        return $this->hasMany(NflQbr::class, 'team_id');
    }

    public function injuries()
    {
        return $this->hasMany(NflInjury::class, 'team_id');
    }

    public function schedules()
    {
        return $this->hasMany(NflTeamSchedule::class, 'team_id_home')
            ->orWhere('team_id_away', $this->id);
    }

    public function players()
    {
        return $this->hasMany(NflPlayer::class, 'team_id');
    }
}
