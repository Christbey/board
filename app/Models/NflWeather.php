<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflWeather extends Model
{
    use HasFactory;

    // Specify the table name if it's different from the class name
    protected $table = 'nfl_weather';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'stadium_id',
        'game_id',
        'date',
        'game_time',
        'temp',
        'wind'
    ];

    // Specify the attributes that should be cast to native types
    protected $casts = [
        'date' => 'date',
        'game_time' => 'datetime:H:i:s',
        'temp' => 'float',
        'wind' => 'float',
    ];

    // Define relationships
    public function stadium()
    {
        return $this->belongsTo(NflStadium::class, 'stadium_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}
