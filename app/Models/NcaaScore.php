<?php

// app/Models/NcaaScore.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NcaaScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'sport_key',
        'sport_title',
        'commence_time',
        'completed',
        'home_team_id',
        'away_team_id',
        'home_team_score',
        'away_team_score',
        'last_update'
    ];

    public function homeTeam()
    {
        return $this->belongsTo(NcaaTeam::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(NcaaTeam::class, 'away_team_id');
    }
}
