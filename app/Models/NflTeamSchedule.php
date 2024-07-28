<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflTeamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'season_type', 'away', 'team_id_home', 'game_date', 'game_status',
        'game_week', 'team_id_away', 'home', 'away_result', 'home_pts', 'game_time',
        'home_result', 'away_pts', 'composite_key'
    ];
    protected $dates = ['game_date'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->composite_key = static::generateCompositeKey($model);
        });
    }

    public static function generateCompositeKey($model): string
    {
        $year = Carbon::parse($model->game_date)->format('Y');

        // Fetch team abbreviations using the team IDs
        $homeTeam = NflTeam::find($model->team_id_home);
        $awayTeam = NflTeam::find($model->team_id_away);

        $homeTeamAbv = $homeTeam ? $homeTeam->abbreviation : 'UNK'; // 'UNK' for unknown abbreviation
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

    // In NflTeamSchedule.php
    public function odds()
    {
        return $this->hasOne(NflOdds::class, 'composite_key', 'composite_key');
    }

    public static function calculateWins($teamId, $seasonStartDate, $seasonEndDate)
    {
        // Count the number of games where the given team was the home team and won in the specified season
        $homeWins = self::where('team_id_home', $teamId)
            ->where('home_result', 'W')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        // Count the number of games where the given team was the away team and won in the specified season
        $awayWins = self::where('team_id_away', $teamId)
            ->where('away_result', 'W')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        // Return the total number of wins
        return $homeWins + $awayWins;
    }

    public static function calculateWinsAndLosses($teamId, $seasonStartDate, $seasonEndDate)
    {
        // Count the number of games where the given team was the home team and won in the specified season
        $homeWins = self::where('team_id_home', $teamId)
            ->where('home_result', 'W')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        // Count the number of games where the given team was the away team and won in the specified season
        $awayWins = self::where('team_id_away', $teamId)
            ->where('away_result', 'W')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        // Count the number of games where the given team was the home team and lost in the specified season
        $homeLosses = self::where('team_id_home', $teamId)
            ->where('home_result', 'L')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        // Count the number of games where the given team was the away team and lost in the specified season
        $awayLosses = self::where('team_id_away', $teamId)
            ->where('away_result', 'L')
            ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
            ->where('season_type', 'Regular Season')
            ->count();

        // Calculate total wins and losses
        $wins = $homeWins + $awayWins;
        $losses = $homeLosses + $awayLosses;

        return ['wins' => $wins, 'losses' => $losses];
    }


}
