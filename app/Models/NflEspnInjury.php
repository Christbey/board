<?php

namespace App\Models;

use App\Jobs\SendDiscordNotificationJob;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\DiscordHelper;
use Illuminate\Support\Facades\Config;

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
            // Determine color based on the team's primary color or default to white
            // Determine color based on injury status
            $color = match ($injury->status) {
                'Active' => '#00FF00',  // Green
                'Questionable' => '#FFFF00',  // Yellow
                'Out', 'Injured Reserve' => '#FF0000',  // Red
                default => '#FFFFFF',  // Default to white if status doesn't match
            };
            $channelId = Config::get('discord.nfl_injury_channel'); // Get the Discord channel ID from the config

            // Create the footer text
            $footerText = "{$injury->status} - {$injury->date}";

            // Create the message using DiscordHelper
            $message = (new DiscordHelper())
                ->setTitle(":medical_symbol: {$injury->athlete->full_name} Injury Report")
                ->setDescription($injury->description)
                ->addField('Team', $injury->team->display_name)
                ->setFooter($footerText)
                ->setColor($color)
                ->build();

            // Dispatch the notification job with a delay
            SendDiscordNotificationJob::dispatch($message, $channelId);
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
