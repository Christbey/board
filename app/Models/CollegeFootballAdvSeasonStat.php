<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballAdvSeasonStat extends Model
{
    use HasFactory;

    protected $table = 'college_football_adv_season_stats';

    protected $fillable = [
        'season', 'team', 'conference',
        'offense_plays', 'offense_drives', 'offense_ppa', 'offense_total_ppa',
        'offense_success_rate', 'offense_explosiveness', 'offense_power_success',
        'offense_stuff_rate', 'offense_line_yards', 'offense_line_yards_total',
        'offense_second_level_yards', 'offense_second_level_yards_total',
        'offense_open_field_yards', 'offense_open_field_yards_total',
        'offense_total_opportunities', 'offense_points_per_opportunity',
        'offense_field_position_average_start', 'offense_field_position_average_predicted_points',
        'offense_havoc_total', 'offense_havoc_front_seven', 'offense_havoc_db',
        'offense_standard_downs_rate', 'offense_standard_downs_ppa',
        'offense_standard_downs_success_rate', 'offense_standard_downs_explosiveness',
        'offense_passing_downs_rate', 'offense_passing_downs_ppa',
        'offense_passing_downs_success_rate', 'offense_passing_downs_explosiveness',
        'offense_rushing_plays_rate', 'offense_rushing_plays_ppa',
        'offense_rushing_plays_total_ppa', 'offense_rushing_plays_success_rate',
        'offense_rushing_plays_explosiveness', 'offense_passing_plays_rate',
        'offense_passing_plays_ppa', 'offense_passing_plays_total_ppa',
        'offense_passing_plays_success_rate', 'offense_passing_plays_explosiveness'
    ];
}
