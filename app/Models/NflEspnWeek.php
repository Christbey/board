<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflEspnWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_year',
        'season_type',
        'week_number',
    ];

    public function events()
    {
        return $this->hasMany(NflEspnEvent::class, 'week_id');
    }
}
