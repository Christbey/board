<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballEloRating extends Model
{
    use HasFactory;

    protected $table = 'college_football_elo_ratings';

    protected $fillable = [
        'year',
        'team',
        'conference',
        'elo',
    ];
}
