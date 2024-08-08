<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballTeam extends Model
{
    use HasFactory;

    protected $table = 'college_football_teams';

    protected $fillable = [
        'school',
        'mascot',
        'abbreviation',
        'alt_name1',
        'alt_name2',
        'alt_name3',
        'conference',
        'classification',
        'color',
        'alt_color',
        'logos',
        'twitter',
        'venue_id',
        'venue_name',
        'city',
        'state',
        'zip',
        'country_code',
        'timezone',
        'latitude',
        'longitude',
        'elevation',
        'capacity',
        'year_constructed',
        'grass',
        'dome',
    ];

    protected $casts = [
        'logos' => 'array',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'grass' => 'boolean',
        'dome' => 'boolean',
    ];
}
