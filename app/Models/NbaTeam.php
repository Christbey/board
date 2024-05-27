<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NbaTeam extends Model
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

    public function homeOdds()
    {
        return $this->hasMany(NbaOdds::class, 'home_team_id');
    }

    public function awayOdds()
    {
        return $this->hasMany(NbaOdds::class, 'away_team_id');
    }
}
