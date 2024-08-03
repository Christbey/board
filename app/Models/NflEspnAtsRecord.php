<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnAtsRecord extends Model
{
    protected $table = 'nfl_espn_ats_records';

    protected $fillable = [
        'team_id',
        'season',
        'type_id',
        'type_name',
        'type_description',
        'wins',
        'losses',
        'pushes',
    ];
}
