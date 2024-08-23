<?php

namespace Tests\Unit\Notifications;

use Illuminate\Notifications\AnonymousNotifiable;
use Tests\TestCase;
use App\Models\NflEspnInjury;
use App\Models\NflEspnTeam;
use App\Models\NflEspnAthlete;
use App\Notifications\EspnInjuryDiscordNotification;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use Illuminate\Support\Facades\Config;

class EspnNewsDiscordNotificationTest extends TestCase
{
    public function testDiscordNotificationIsSent()
    {
        // Arrange: Create mock instances of your models
        $team = NflEspnTeam::factory()->create(['display_name' => 'Test Team']);
        $athlete = NflEspnAthlete::factory()->create(['full_name' => 'John Doe']);

        $injury = NflEspnInjury::factory()->create([
            'team_id' => $team->team_id,
            'athlete_id' => $athlete->athlete_id,
            'status' => 'Questionable',
            'description' => 'Knee injury',
            'date' => now()->toDateString(),
        ]);

        // Act: Fake notifications and trigger the notification
        Notification::fake();

        // Call the notification method
        Notification::route('discord', Config::get('discord.nfl_injury_channel'))
            ->notify(new EspnInjuryDiscordNotification($injury));

        // Assert: Check that the notification was sent
        Notification::assertSentTo(
            new AnonymousNotifiable,
            EspnInjuryDiscordNotification::class,
            function ($notification, $channels) use ($injury) {
                // Assert the notification was sent to the correct channel
                $this->assertContains(DiscordChannel::class, $channels);

                // Assert the notification contains the correct data
                $message = $notification->toDiscord($injury);
                $this->assertInstanceOf(DiscordMessage::class, $message);
                $this->assertStringContainsString('John Doe Injury Report', $message->embed['title']);
                $this->assertEquals('Knee injury', $message->embed['description']);
                $this->assertEquals(hexdec('FFFF00'), $message->embed['color']); // Yellow for 'Questionable'

                return true;
            }
        );
    }
}
