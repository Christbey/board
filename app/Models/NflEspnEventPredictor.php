<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflEspnEventPredictor extends Model
{
    use HasFactory;

    protected $table = 'nfl_espn_event_predictors';
    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'event_id',
        'name',
        'short_name',
        'last_modified',
        'home_team_id',
        'away_team_id',
        'home_gameProjection',
        'home_matchupQuality',
        'home_oppSeasonStrengthFbsRank',
        'home_oppSeasonStrengthRating',
        'home_teamAvgWp',
        'home_teamChanceLoss',
        'home_teamChanceTie',
        'home_teamDefEff',
        'home_teamOffEff',
        'home_teamPredPtDiff',
        'home_teamSTEff',
        'home_teamTotEff',
        'away_gameProjection',
        'away_matchupQuality',
        'away_oppSeasonStrengthFbsRank',
        'away_oppSeasonStrengthRating',
        'away_teamAvgWp',
        'away_teamChanceLoss',
        'away_teamChanceTie',
        'away_teamDefEff',
        'away_teamOffEff',
        'away_teamPredPtDiff',
        'away_teamSTEff',
        'away_teamTotEff',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(NflEspnTeam::class, 'home_team_id', 'team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(NflEspnTeam::class, 'away_team_id', 'team_id');
    }
}
