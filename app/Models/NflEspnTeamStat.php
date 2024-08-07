<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflEspnTeamStat extends Model
{
    use HasFactory;

    protected $table = 'nfl_espn_team_stats';

    protected $fillable = [
        'season',
        'team_id',
        'category',
        'stat_name',
        'stat_value',
        'stat_display_value',
        'stat_rank',
        'stat_rank_display_value'
    ];
}
