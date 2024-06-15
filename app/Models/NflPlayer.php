<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflPlayer extends Model
{
    use HasFactory;

    protected $table = 'nfl_players';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'player_id', 'long_name', 'team', 'position', 'age', 'longName', 'jerseyNum', 'pos', 'school',  'exp', 'height', 'weight' // and other columns
    ];

    public function playerStats()
    {
        return $this->hasMany(NflPlayerStat::class, 'player_id', 'player_id');
    }

    public function injuries()
    {
        return $this->hasMany(NflInjury::class, 'player_id', 'player_id');
    }
}
