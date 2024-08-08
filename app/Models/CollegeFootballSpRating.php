<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballSpRating extends Model
{
    use HasFactory;

    protected $table = 'college_football_sp_ratings';

    protected $fillable = [
        'year',
        'team',
        'conference',
        'rating',
        'ranking',
        'second_order_wins',
        'sos',
        'offense_ranking',
        'offense_rating',
        'offense_success',
        'offense_explosiveness',
        'offense_rushing',
        'offense_passing',
        'offense_standard_downs',
        'offense_passing_downs',
        'offense_run_rate',
        'offense_pace',
        'defense_ranking',
        'defense_rating',
        'defense_success',
        'defense_explosiveness',
        'defense_rushing',
        'defense_passing',
        'defense_standard_downs',
        'defense_passing_downs',
        'defense_havoc_total',
        'defense_havoc_front_seven',
        'defense_havoc_db',
        'special_teams_rating',
    ];
}
