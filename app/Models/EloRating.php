<?php

namespace App\Models;

use App\Helpers\DiscordHelper;
use App\Jobs\SendDiscordNotificationJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EloRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'rating'
    ];

    protected static function booted()
    {
        static::created(function ($eloRating) {

            $message = (new DiscordHelper())
                ->setTitle(':football: New ELO Rating')
                ->addField('elo', $eloRating->rating)
                ->build();

            // Dispatch the notification job with a delay
            SendDiscordNotificationJob::dispatch($message)->delay(now()->addSeconds(5));
        });
    }

}
