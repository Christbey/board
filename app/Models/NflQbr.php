<?php

namespace App\Models;

use App\Notifications\DiscordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\DiscordHelper;
use App\Models\NflPlayer;
use App\Jobs\SendDiscordNotificationJob;
use Illuminate\Notifications\Notifiable;

class NflQbr extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'nfl_qbr';

    protected $fillable = [
        'team_id',
        'player_id',
        'qbr',
        'attempts',
        'completions',
        'game_id',
        'passing_yards',
        'passing_touchdowns',
        'interceptions',
    ];

    /**
     * Get the team that owns the QBR.
     */
    public function team()
    {
        return $this->belongsTo(NflTeam::class, 'team_id');
    }

    /**
     * Get the player associated with the QBR.
     */
    public function player()
    {
        return $this->belongsTo(NflPlayer::class, 'player_id');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(function ($qbr) {
            // Ensure the player relationship is loaded
            $qbr->load('player', 'team'); // Load both player and team relationships

            $message = (new DiscordHelper())
                ->setTitle('Quarterback Rating')
                ->setDescription($qbr->created_at->format('Y-m-d H:i:s'))
                ->addField('Player', $qbr->player->longName)
                ->addField('Rating', (string)$qbr->qbr)
                ->setColor($qbr->team->primary_color ?? '#000000') // Default to black if no primary color
                ->build();

            // Dispatch the notification job with a delay
            SendDiscordNotificationJob::dispatch($message)->delay(now()->addSeconds(5));
        });
    }

    /**
     * Ensure the player exists in the database.
     */
    protected function ensurePlayerExists()
    {
        if (!NflPlayer::find($this->player_id)) {
            $playerData = $this->fetchPlayerDataFromApi($this->player_id);

            if ($playerData) {
                NflPlayer::create([
                    'id' => $playerData['id'],
                    'name' => $playerData['name'],
                    // Add other player fields as necessary
                ]);

                $this->refresh();
            }
        }
    }

    /**
     * Fetch player data from API or another data source.
     *
     * @param int $playerId
     * @return array|null
     */
    protected function fetchPlayerDataFromApi($playerId)
    {
        // Implement API call to fetch player data
        return [
            'id' => $playerId,
            'name' => 'Sample Player', // Replace with actual fetched data
            // Add other fields as necessary
        ];
    }
}
