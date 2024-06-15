<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflInjury extends Model
{
    use HasFactory;

    protected $table = 'nfl_injuries';

    protected $fillable = [
        'player_id',
        'team_id',
        'injury_type',
        'injury_date',
        'designation',
        'description',
    ];

    public function player()
    {
        return $this->belongsTo(NflPlayer::class, 'player_id', 'player_id');
    }

    public function team()
    {
        return $this->belongsTo(NflTeam::class, 'team_id');
    }
}
