<?php

namespace App\Models;

use App\DiscordNotifier;
use App\Notifications\NflOddsUpdateNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

class NflOdds extends Model
{
    use HasFactory, Notifiable;

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

    protected static function booted()
    {
        static::updated(function ($odds) {
            // Send the notification when the odds are updated
            $notifier = new DiscordNotifier('nfl_odds_channel');
            $notifier->notify($odds, new NflOddsUpdateNotification($odds));
        });
    }


    public static function generateCompositeKey($model): string
    {
        $year = Carbon::parse($model->commence_time)->format('Y');
        $homeTeam = NflTeam::find($model->home_team_id);
        $awayTeam = NflTeam::find($model->away_team_id);

        $homeTeamAbv = $homeTeam ? $homeTeam->abbreviation : 'UNK';
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
