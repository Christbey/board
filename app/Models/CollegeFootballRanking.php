<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballRanking extends Model
{
    use HasFactory;

    protected $table = 'college_football_rankings';

    protected $fillable = [
        'season',
        'season_type',
        'week',
        'poll',
        'rank',
        'school',
        'conference',
        'first_place_votes',
        'points',
    ];
}
