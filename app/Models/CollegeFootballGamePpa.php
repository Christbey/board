<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballGamePpa extends Model
{
    use HasFactory;

    protected $table = 'college_football_game_ppa';

    protected $fillable = [
        'game_id',
        'season',
        'week',
        'team',
        'conference',
        'opponent',
        'offense_overall',
        'offense_passing',
        'offense_rushing',
        'offense_first_down',
        'offense_second_down',
        'offense_third_down',
        'defense_overall',
        'defense_passing',
        'defense_rushing',
        'defense_first_down',
        'defense_second_down',
        'defense_third_down',
    ];
}
