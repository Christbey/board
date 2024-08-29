<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\EspnInjuryDiscordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;

class NflEspnInjury extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'nfl_espn_injuries';

    protected $fillable = [
        'team_id',
        'athlete_id',
        'injury_id',
        'type',
        'status',
        'date',
        'description',
    ];


    protected static function booted()
    {
        static::created(function ($injury) {
            // Send the notification directly when a new injury is created
            Notification::send($injury, new EspnInjuryDiscordNotification($injury));
        });
    }


    public function team()
    {
        return $this->belongsTo(NflEspnTeam::class, 'team_id', 'team_id');
    }

    public function athlete()
    {
        return $this->belongsTo(NflEspnAthlete::class, 'athlete_id', 'athlete_id');
    }
}

# Tinker command to create a new injury:
# $injury = \App\Models\NflEspnInjury::create([
#'team_id' => 1,
#'athlete_id' => 4242547, // Provided athlete_id
#'injury_id' => 1,
#'type' => 'Leg',
#'status' => 'Questionable',
#'description' => 'Knee injury',
#'date' => '2024-08-21',
#]);
