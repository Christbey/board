<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NflEspnSplit extends Model
{
    protected $table = 'nfl_espn_splits';

    protected $fillable = [
        'athlete_id',
        'split_category',
        'split_type',
        'display_name',
        'year',
        'TOT',
        'SOLO',
        'AST',
        'SACK',
        'STF',
        'STFYDS',
        'FF',
        'FR',
        'KB',
        'INT',
        'YDS',
        'AVG',
        'TD',
        'LNG',
        'PD',
    ];
}
