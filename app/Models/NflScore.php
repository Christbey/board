<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflScore extends Model
{
    use HasFactory;

    // The attributes that are mass assignable.
    protected $fillable = [
        'event_id',
        'sport_key',
        'sport_title',
        'commence_time',
        'home_team_id',
        'away_team_id',
        'home_team_score',
        'away_team_score',
        'last_update',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'commence_time' => 'datetime',
        'last_update' => 'datetime',
    ];

    // Define relationships if needed
    public function homeTeam()
    {
        return $this->belongsTo(NflTeam::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(NflTeam::class, 'away_team_id');
    }
}
