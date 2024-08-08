<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballRoster extends Model
{
    use HasFactory;

    protected $table = 'college_football_rosters';

    protected $fillable = [
        'player_id',
        'first_name',
        'last_name',
        'team',
        'weight',
        'height',
        'jersey',
        'year',
        'position',
        'home_city',
        'home_state',
        'home_country',
        'home_latitude',
        'home_longitude',
        'home_county_fips',
        'recruit_ids',
    ];

    protected $casts = [
        'home_latitude' => 'decimal:7',
        'home_longitude' => 'decimal:7',
        'recruit_ids' => 'array',
    ];
}
