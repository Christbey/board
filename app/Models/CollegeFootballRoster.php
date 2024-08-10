<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballRoster extends Model
{
    use HasFactory;

    protected $table = 'college_football_rosters';

    protected $primaryKey = 'player_id'; // Set player_id as the primary key
    public $incrementing = false; // Disable auto-incrementing since player_id is not an integer
    protected $keyType = 'string'; // Set the type of the primary key

    protected $fillable = [
        'player_id',
        'first_name',
        'last_name',
        'team_id', // Change from 'team' to 'team_id'
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

    // Define the relationship to the CollegeFootballTeam model
    public function team()
    {
        return $this->belongsTo(CollegeFootballTeam::class, 'team_id');
    }
}
