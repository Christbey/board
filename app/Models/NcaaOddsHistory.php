<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NcaaOddsHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'odds_id',
        'h2h_home_price',
        'h2h_away_price',
        'spread_home_point',
        'spread_away_point',
        'spread_home_price',
        'spread_away_price',
        'total_over_point',
        'total_under_point',
        'total_over_price',
        'total_under_price',

    ];


}