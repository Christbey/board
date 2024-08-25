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
        // Weather data fields
        'weather_type', 'weather_display_value', 'weather_zip_code', 'weather_last_updated',
        'weather_wind_speed', 'weather_wind_direction', 'weather_temperature',
        'weather_high_temperature', 'weather_low_temperature', 'weather_condition_id',
        'weather_gust', 'weather_precipitation', 'weather_link',
    ];

    protected $casts = [
        'attendance' => 'integer',
        'neutral_site' => 'boolean',
        'conference_competition' => 'boolean',
        'play_by_play_available' => 'boolean',
        'venue_indoor' => 'boolean',
        'home_team_score' => 'integer',
        'away_team_score' => 'integer',
        'status_type_completed' => 'boolean',
        'weather_wind_speed' => 'integer',
        'weather_temperature' => 'integer',
        'weather_high_temperature' => 'integer',
        'weather_low_temperature' => 'integer',
        'weather_condition_id' => 'integer',
        'weather_gust' => 'integer',
        'weather_precipitation' => 'integer',
        'date' => 'datetime',
        'weather_last_updated' => 'datetime',

    ];


    public function awayTeam()
    {
        return $this->belongsTo(NflEspnTeam::class, 'away_team_id');
    }

    public function homeTeam()
    {
        return $this->belongsTo(NflEspnTeam::class, 'home_team_id');
    }


    public function week()
    {
        return $this->belongsTo(NflEspnWeek::class, 'week_id');
    }

//    public function showWeekEvents($week_id = null)
//    {
//        $weeks = NflEspnWeek::all(); // Load all weeks for the dropdown
//
//        // Load events for the selected week, or all events if no week is selected
//        $events = NflEspnEvent::when($week_id, function ($query) use ($week_id) {
//            return $query->where('week_id', $week_id);
//        })->with(['awayTeam', 'homeTeam', 'week'])->get();
//
//        return view('nfl.picks.week', compact('events', 'weeks', 'week_id'));
//    }

}
