<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'week_id',
        'team_id',
        'is_correct',
    ];

    /**
     * Get the user that made the submission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event associated with the submission.
     */
    public function event()
    {
        return $this->belongsTo(NflEspnEvent::class, 'event_id');
    }

    /**
     * Get the team that the user selected.
     */
    public function team()
    {
        return $this->belongsTo(NflEspnTeam::class, 'team_id');
    }
}
