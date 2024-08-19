<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DiscordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDiscordNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param mixed $message
     * @param int|null $userId
     * @return void
     */
    public function __construct($message, int $userId = null)
    {
        $this->message = $message;
        $this->userId = $userId ?? 1; // Default to user ID 1 if not provided
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Find the user to notify
        $user = User::find($this->userId);

        // If user is found, send the notification
        $user?->notify(new DiscordNotification($this->message));

        // Optionally, implement rate-limiting logic if necessary, instead of sleep
    }
}
