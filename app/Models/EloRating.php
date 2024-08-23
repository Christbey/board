<?php

namespace App\Models;

use App\Notifications\EloRatingDiscordNotification;
use App\DiscordNotifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EloRating extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'team_id',
        'rating'
    ];


    // Define the relationship with the NflTeam model
    public function team()
    {
        return $this->belongsTo(NflTeam::class, 'team_id', 'id');
    }

    // Use the relationship to get the team name
    public function getTeamNameAttribute()
    {
        return $this->team ? $this->team->name : 'Unknown Team';
    }


}
