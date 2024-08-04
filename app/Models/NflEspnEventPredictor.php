<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflEspnEventPredictor extends Model
{
    use HasFactory;

    protected $table = 'nfl_espn_event_predictors';

    protected $fillable = [
        'rank', 'total', 'value', 'display_value', 'predictor_competition_id', 'projected_winner_id', 'cover_id', 'projected_cover_id'
    ];
}
