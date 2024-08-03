<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnTeam extends Model
{
    protected $table = 'nfl_espn_teams';

    protected $primaryKey = 'team_id';

    public $incrementing = false;

    protected $fillable = [
        'team_id',
        'uid',
        'slug',
        'abbreviation',
        'display_name',
        'short_display_name',
        'name',
        'nickname',
        'location',
        'color',
        'alternate_color',
        'is_active',
    ];
}
