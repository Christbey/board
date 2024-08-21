<?php

namespace App\Models;

use App\Helpers\DiscordHelper;
use App\Jobs\SendDiscordNotificationJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Log;

class NflTeamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'season_type', 'away', 'team_id_home', 'game_date', 'game_status',
        'game_week', 'team_id_away', 'home', 'away_result', 'home_pts', 'game_time',
        'home_result', 'away_pts', 'composite_key'
    ];
    protected $dates = ['game_date'];
    public $away_result;
    public $season;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->composite_key = static::generateCompositeKey($model);
        });
    }

    protected static function booted()
    {
        static::updated(function ($schedule) {
            NflTeamSchedule::sendDiscordNotification($schedule);
        });
    }

    public static function sendDiscordNotification($schedule)
    {
        // Ensure the home and away team relationships are loaded
        $schedule->load('homeTeam', 'awayTeam');
        $channelId = Config::get('discord.nfl_injury'); // Get the Discord channel ID from the config

        $homeMascot = $schedule->homeTeam->team_mascot ?? 'Unknown';
        $awayMascot = $schedule->awayTeam->team_mascot ?? 'Unknown';
        $homeColor = $schedule->homeTeam->primary_color ?? '#000000'; // Default to black if no color is set
        $awayColor = $schedule->awayTeam->primary_color ?? '#000000'; // Default to black if no color is set

        if ($schedule->home_pts > $schedule->away_pts) {
            $color = $homeColor; // Home team is winning
        } elseif ($schedule->away_pts > $schedule->home_pts) {
            $color = $awayColor; // Away team is winning
        } else {
            $color = '#FFFF00'; // Yellow if the scores are tied
        }
        // Build the Discord message
        $message = (new DiscordHelper())
            ->setTitle('Game Break!')
            ->addField('Status', (string)$schedule->game_status)
            ->addField('Home', "{$homeMascot} {$schedule->home_pts}", true)
            ->addField('Away', "{$awayMascot} {$schedule->away_pts}", true)
            ->setColor($color)
            ->build();

        // Dispatch the notification job with the constructed message
        SendDiscordNotificationJob::dispatch($message, $channelId);
    }


    public static function generateCompositeKey($model): string
    {
        $year = Carbon::parse($model->game_date)->format('Y');

        $homeTeam = NflTeam::find($model->team_id_home);
        $awayTeam = NflTeam::find($model->team_id_away);

        $homeTeamAbv = $homeTeam ? $homeTeam->abbreviation : 'UNK';
        $awayTeamAbv = $awayTeam ? $awayTeam->abbreviation : 'UNK';

        return "{$year}_{$homeTeamAbv}_{$awayTeamAbv}";
    }

    public function homeTeam()
    {
        return $this->belongsTo(NflTeam::class, 'team_id_home');
    }

    public function awayTeam()
    {
        return $this->belongsTo(NflTeam::class, 'team_id_away');
    }

    public function playerStats()
    {
        return $this->hasMany(NflPlayerStat::class, 'game_id', 'game_id');
    }

    public function odds()
    {
        return $this->hasOne(NflOdds::class, 'composite_key', 'composite_key');
    }

    public static function calculateWins($teamId, $seasonStartDate, $seasonEndDate)
    {
        $homeWins = self::where('team_id_home', $teamId)
            ->where('home_result', 'W')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        $awayWins = self::where('team_id_away', $teamId)
            ->where('away_result', 'W')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        return $homeWins + $awayWins;
    }

    public static function calculateWinsAndLosses($teamId, $seasonStartDate, $seasonEndDate)
    {
        $homeWins = self::where('team_id_home', $teamId)
            ->where('home_result', 'W')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        $awayWins = self::where('team_id_away', $teamId)
            ->where('away_result', 'W')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        $homeLosses = self::where('team_id_home', $teamId)
            ->where('home_result', 'L')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        $awayLosses = self::where('team_id_away', $teamId)
            ->where('away_result', 'L')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        return ['wins' => $homeWins + $awayWins, 'losses' => $homeLosses + $awayLosses];
    }
}
