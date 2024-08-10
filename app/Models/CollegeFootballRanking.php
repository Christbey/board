<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'season',
        'season_type',
        'week',
        'poll',
        'rank',
        'team_id',
        'conference_id',
        'first_place_votes',
        'points',
    ];

    // Define relationships if needed
    public function team()
    {
        return $this->belongsTo(CollegeFootballTeam::class, 'team_id');
    }

    public function conference()
    {
        return $this->belongsTo(CollegeFootballConference::class, 'conference_id');
    }
}
