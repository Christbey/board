<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeHypotheticalSpread extends Model
{
    // The table associated with the model
    protected $table = 'college_hypothetical_spreads';

    // The attributes that are mass assignable
    protected $fillable = [
        'game_id',
        'spread',
        'correct',
        'home_team',
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'correct' => 'boolean',
    ];

    // Relationships or any custom methods can be added here if needed
}
