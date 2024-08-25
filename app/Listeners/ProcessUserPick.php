<?php

namespace App\Listeners;

use App\Events\UserMadePick;
use App\Models\UserSubmission;

class ProcessUserPick
{
    public function handle(UserMadePick $event)
    {
        // Ensure is_correct is always a boolean
        $isCorrect = $event->isCorrect ?? false;

        // Check if a submission already exists for the user, week_id, and event_id
        $submission = UserSubmission::where('user_id', $event->user->id)
            ->where('week_id', $event->event->week_id)
            ->where('event_id', $event->event->id)
            ->first();

        if ($submission) {
            // Update the existing submission
            $submission->update([
                'event_id' => $event->event->id,
                'team_id' => $event->selectedTeamId,
                'is_correct' => $isCorrect,
            ]);
        } else {
            // Create a new submission
            UserSubmission::create([
                'user_id' => $event->user->id,
                'event_id' => $event->event->id,
                'week_id' => $event->event->week_id,
                'team_id' => $event->selectedTeamId,
                'is_correct' => $isCorrect,
            ]);
        }
    }
}
