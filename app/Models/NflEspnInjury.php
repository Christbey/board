<?php

namespace App\Models;

use App\Jobs\SendDiscordNotificationJob;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\DiscordHelper;

class NflEspnInjury extends Model
{
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
            // Determine color based on injury status
            $color = match ($injury->status) {
                'Active' => '#00FF00',  // Green
                'Questionable' => '#FFFF00',  // Yellow
                'Out', 'Injured Reserve' => '#FF0000',  // Red
                default => '#FFFFFF',  // Default to white if status doesn't match
            };

            // Create the message using DiscordHelper
            $message = (new DiscordHelper())
                ->setTitle(':medical_symbol: Injury Report $injury->team->display_name ')
                ->addField('Athlete', $injury->athlete->full_name)
                ->setDescription($injury->description)
                ->addField('Team', $injury->team->display_name)
                ->addField('Status', $injury->status)
                ->addField('Date', $injury->date)
                ->setColor($color)
                ->build();

            // Dispatch the notification job with a delay
            SendDiscordNotificationJob::dispatch($message)->delay(now()->addSeconds(5));
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
