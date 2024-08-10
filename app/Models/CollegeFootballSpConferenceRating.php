<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballSpConferenceRating extends Model
{
    use HasFactory;

    protected $table = 'college_football_sp_conference_ratings';

    protected $fillable = [
        'year',
        'conference_id',
        'rating',
        'ranking',
        'offense_rating',
        'defense_rating',
        'special_teams_rating',
    ];

    public function conference()
    {
        return $this->belongsTo(CollegeFootballConference::class, 'conference_id');
    }
}
