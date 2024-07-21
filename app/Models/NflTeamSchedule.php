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

}
