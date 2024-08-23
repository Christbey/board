<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflStadium extends Model
{
    use HasFactory;

    // Specify the table name if it's different from the class name
    protected $table = 'nfl_stadiums';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'stadium_name',
        'team_id',
        'city',
        'state',
        'roof_type',
        'longitude',
        'latitude',
        'stadium_azimuth_angle',
        'active'
    ];

    // Specify the attributes that should be cast to native types
    protected $casts = [
        'longitude' => 'float',
        'latitude' => 'float',
        'stadium_azimuth_angle' => 'float',
        'active' => 'boolean',
    ];

    // Define relationships
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function weathers()
    {
        return $this->hasMany(NflWeather::class, 'stadium_id');
    }

    /**
     * Calculate the distance between two points (latitude and longitude).
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Distance in kilometers

        return $distance;
    }
}
