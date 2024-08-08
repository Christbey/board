<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballAdvGameStat extends Model
{
    use HasFactory;

    protected $table = 'college_football_adv_game_stats';

    protected $fillable = [
        'game_id', 'season', 'week', 'team', 'opponent',
        'offense_plays', 'offense_drives', 'offense_ppa', 'offense_total_ppa',
        'offense_success_rate', 'offense_explosiveness', 'offense_power_success',
        'offense_stuff_rate', 'offense_line_yards', 'offense_line_yards_total',
        'offense_second_level_yards', 'offense_second_level_yards_total',
        'offense_open_field_yards', 'offense_open_field_yards_total',
        'offense_standard_downs_ppa', 'offense_standard_downs_success_rate',
        'offense_standard_downs_explosiveness', 'offense_passing_downs_ppa',
        'offense_passing_downs_success_rate', 'offense_passing_downs_explosiveness',
        'offense_rushing_plays_ppa', 'offense_rushing_plays_total_ppa',
        'offense_rushing_plays_success_rate', 'offense_rushing_plays_explosiveness',
        'offense_passing_plays_ppa', 'offense_passing_plays_total_ppa',
        'offense_passing_plays_success_rate', 'offense_passing_plays_explosiveness'
    ];
}
