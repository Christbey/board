<?php

namespace App\Listeners;

use App\Events\OddsChanged;
use App\Helpers\DiscordHelper;
use App\Models\User;
use App\Notifications\DiscordNotification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Discord\Exceptions\CouldNotSendNotification;
use Exception;

class SendOddsChangedNotification
{
    public function handle(OddsChanged $event)
    {
        try {
            $description = $this->generateDescription($event);

            if (!empty(trim($description))) {
                $message = (new DiscordHelper())
                    ->setTitle("{$event->homeTeam->name} {$event->newOdds['spread_home_point']} vs {$event->awayTeam->name} {$event->newOdds['spread_away_point']}")
                    ->setDescription($description)
                    ->setColor($event->homeTeam->color)
                    ->build();

                $user = $this->getNotificationUser();

                if ($user) {
                    $user->notify(new DiscordNotification($message));
                }
            }

            Log::info('Notification sent to Discord successfully');
        } catch (CouldNotSendNotification $e) {
            if ($e->getCode() == 429) {
                Log::warning('Rate limited by Discord: ', ['error' => $e->getMessage()]);
                sleep(60); // Optionally, sleep for 60 seconds
            } else {
                Log::error('Failed to send notification to Discord', ['error' => $e->getMessage()]);
            }
        } catch (Exception $e) {
            Log::error('An unexpected error occurred while sending notification to Discord', ['error' => $e->getMessage()]);
        }
    }

    protected function generateDescription(OddsChanged $event): string
    {
        $description = "**Odds have changed!**\n";

        $this->addDescriptionLine($description, 'total_over_point', 'Total', $event);
        $this->addDescriptionLine($description, 'h2h_home_price', 'Home Team H2H Price', $event);
        $this->addDescriptionLine($description, 'h2h_away_price', 'Away Team H2H Price', $event);
        $this->addDescriptionLine($description, 'spread_home_point', 'Home Spread Point', $event);
        $this->addDescriptionLine($description, 'spread_away_point', 'Away Spread Point', $event);

        // Adding boolean checks
        $this->addBooleanDescriptionLine($description, 'neutral_site', 'Neutral Site', $event);
        $this->addBooleanDescriptionLine($description, 'conference_competition', 'Conference Competition', $event);
        $this->addBooleanDescriptionLine($description, 'play_by_play_available', 'Play-by-Play Available', $event);
        $this->addBooleanDescriptionLine($description, 'venue_indoor', 'Venue Indoor', $event);

        return $description;
    }

    protected function addDescriptionLine(string &$description, string $key, string $label, OddsChanged $event): void
    {
        if (number_format($event->existingOdds->$key, 2) !== number_format($event->newOdds[$key], 2)) {
            $emoji = $event->newOdds[$key] > $event->existingOdds->$key ? '⬆️' : '⬇️';
            $description .= "~~Old {$label}: {$event->existingOdds->$key}~~ ➔ New {$label}: {$event->newOdds[$key]} {$emoji}\n";
        }
    }

    protected function addBooleanDescriptionLine(string &$description, string $key, string $label, OddsChanged $event): void
    {
        $oldValue = $event->existingOdds->$key ? 'True' : 'False';
        $newValue = $event->newOdds[$key] ? 'True' : 'False';

        if ($oldValue !== $newValue) {
            $description .= "~~Old {$label}: {$oldValue}~~ ➔ New {$label}: {$newValue}\n";
        }
    }

    protected function getNotificationUser()
    {
        return User::find(1); // Replace with your logic to determine the user to notify
    }
}
