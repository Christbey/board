<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflOdds extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'sport_title',
        'sport_key',
        'home_team_id',
        'away_team_id',
        'h2h_home_price',
        'h2h_away_price',
        'spread_home_point',
        'spread_away_point',
        'spread_home_price',
        'spread_away_price',
        'total_over_point',
        'total_under_point',
        'total_over_price',
        'total_under_price',
        'commence_time',
        'bookmaker_key',
        'composite_key',
    ];

    protected $dates = ['commence_time'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->composite_key = static::generateCompositeKey($model);
        });
    }

    public static function generateCompositeKey($model): string
    {
        $year = Carbon::parse($model->commence_time)->format('Y');

        // Fetch team abbreviations using the team IDs
        $homeTeam = NflTeam::find($model->home_team_id);
        $awayTeam = NflTeam::find($model->away_team_id);

        $homeTeamAbv = $homeTeam ? $homeTeam->abbreviation : 'UNK'; // 'UNK' for unknown abbreviation
        $awayTeamAbv = $awayTeam ? $awayTeam->abbreviation : 'UNK';

        return "{$year}_{$homeTeamAbv}_{$awayTeamAbv}";
    }


    public function homeTeam()
    {
        return $this->belongsTo(NflTeam::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(NflTeam::class, 'away_team_id');
    }

    public function history()
    {
        return $this->hasMany(NflOddsHistory::class, 'odds_id');
    }

    public function teamSchedule()
    {
        return $this->belongsTo(NflTeamSchedule::class, 'composite_key', 'composite_key');
    }


    public function schedule()
    {
        return $this->belongsTo(NflTeamSchedule::class, 'composite_key', 'composite_key');
    }
}

