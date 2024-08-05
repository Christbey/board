<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflEspnNews extends Model
{
    use HasFactory;

    protected $table = 'nfl_espn_news';

    protected $fillable = [
        'headline',
        'description',
        'url',
        'image_url',
        'byline',
        'published',
        'last_modified',
        'team_id',
        'athlete_id',
    ];
}
