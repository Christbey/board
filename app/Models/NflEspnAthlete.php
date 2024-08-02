<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnAthlete extends Model
{
    protected $table = 'nfl_espn_athletes';

    protected $fillable = [
        'athlete_id',
        'team_id',
        'season_year',
        'jersey',
        'uid',
        'guid',
        'first_name',
        'last_name',
        'full_name',
        'display_name',
        'short_name',
        'weight',
        'display_weight',
        'height',
        'display_height',
        'age',
        'date_of_birth',
        'debut_year',
        'position',
        'status',
    ];

    public function depthCharts()
    {
        return $this->hasMany(EspnNflDepthChart::class, 'athlete_id', 'athlete_id');
    }
}
