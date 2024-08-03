<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnFuture extends Model
{
    protected $table = 'nfl_espn_futures';

    protected $fillable = [
        'future_id',
        'name',
        'display_name',
        'provider_id',
        'provider_name',
        'athlete_id',
        'team_id',
        'value',
    ];
}
