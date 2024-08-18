<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NflEspnNews extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'nfl_espn_news';

    protected $fillable = [
        'headline',
        'description',
        'url',
        'image_url',
        'byline',
        'published',
        'last_modified',
        'team_id',
        'athlete_id',
    ];

    /**
     * Get the Discord channel ID to send notifications to.
     *
     * @return string
     */
    public function routeNotificationForDiscord()
    {
        return $this->discord_private_channel_id;
    }
}
