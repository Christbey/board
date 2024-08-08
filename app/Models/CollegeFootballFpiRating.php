<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballFpiRating extends Model
{
    use HasFactory;

    protected $table = 'college_football_fpi_ratings';

    protected $fillable = [
        'year',
        'team',
        'conference',
        'fpi',
        'strength_of_record',
        'resume_fpi',
        'average_win_probability',
        'strength_of_schedule',
        'remaining_strength_of_schedule',
        'game_control',
        'efficiency_overall',
        'efficiency_offense',
        'efficiency_defense',
        'efficiency_special_teams',
    ];
}
