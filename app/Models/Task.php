<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id',
        'task',
        'completed',
        'status',
        'priority',
        'reminder_date',
        'due_date',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'completed' => 'boolean',
        'reminder_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor to format the reminder date
    public function getFormattedReminderDateAttribute()
    {
        return $this->reminder_date ? Carbon::parse($this->reminder_date)->format('m/d/Y H:i') : null;
    }

    // Accessor to format the due date
    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? Carbon::parse($this->due_date)->format('m/d/Y H:i') : null;
    }
}
