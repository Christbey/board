<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflNews extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'link', 'team_id', 'player_id',
    ];
}
