<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class NflEspnEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'week_id', 'event_id', 'uid', 'date', 'name', 'short_name', 'attendance',
        'neutral_site', 'conference_competition', 'play_by_play_available', 'venue_id',
        'venue_name', 'venue_city', 'venue_state', 'venue_indoor', 'status_type_id',
        'status_type_name', 'status_type_state', 'status_type_completed',
        'status_type_description', 'status_type_detail', 'status_type_short_detail',
        'home_team_id', 'away_team_id', 'home_team_score', 'away_team_score', 'home_team_record', 'away_team_record',
        
    ];
}
