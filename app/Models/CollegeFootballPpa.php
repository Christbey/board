<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballPpa extends Model
{
    use HasFactory;

    protected $table = 'college_football_ppa';

    protected $fillable = [
        'season',
        'team',
        'conference',
        'offense_overall',
        'offense_passing',
        'offense_rushing',
        'offense_first_down',
        'offense_second_down',
        'offense_third_down',
        'offense_cumulative_total',
        'offense_cumulative_passing',
        'offense_cumulative_rushing',
        'defense_overall',
        'defense_passing',
        'defense_rushing',
        'defense_first_down',
        'defense_second_down',
        'defense_third_down',
        'defense_cumulative_total',
        'defense_cumulative_passing',
        'defense_cumulative_rushing',
    ];
}
