<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'school',
        'mascot',
        'abbreviation',
        'alt_name1',
        'alt_name2',
        'alt_name3',
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
        'conference_id', // Add this to link with CollegeFootballConference
    ];

    public function conference()
    {
        return $this->belongsTo(CollegeFootballConference::class, 'conference_id');
    }

    // CollegeFootballTeam.php
    public function ncaaTeam()
    {
        return $this->belongsTo(NcaaTeam::class, 'ncaa_team_id');
    }

}
