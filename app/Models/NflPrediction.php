<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'team_id_home',
        'team_id_away',
        'game_date',
        'home_pts_prediction',
        'away_pts_prediction',
        'home_win_percentage',
        'away_win_percentage',
        'season_type',
    ];

    public static function createOrUpdate($attributes)
    {
        $prediction = static::where('game_id', $attributes['game_id'])->first();

        if ($prediction) {
            $prediction->update($attributes);
        } else {
            $prediction = static::create($attributes);
        }

        return $prediction;
    }
}
