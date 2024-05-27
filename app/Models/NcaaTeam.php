<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NcaaTeam extends Model
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
        return $this->hasMany(NcaaOdds::class, 'home_team_id');
    }

    public function awayOdds()
    {
        return $this->hasMany(NcaaOdds::class, 'away_team_id');
    }
}
