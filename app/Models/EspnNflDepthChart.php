<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EspnNflDepthChart extends Model
{
    use HasFactory;

    protected $table = 'espn_nfl_depth_chart';

    protected $fillable = [
        'team_id',
        'athlete_id',
        'position',
        'depth',
    ];

    public function athlete()
    {
        return $this->belongsTo(NflEspnAthlete::class, 'athlete_id', 'athlete_id');
    }
}
