<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballTalent extends Model
{
    use HasFactory;

    protected $table = 'college_football_talents';

    protected $primaryKey = 'team_id'; // Set team_id as the primary key

    public $incrementing = false; // Disable auto-incrementing as team_id is not auto-incremented

    protected $fillable = [
        'team_id',
        'year',
        'school',
        'talent',
    ];
}
