<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballTalent extends Model
{
    use HasFactory;

    protected $table = 'college_football_talents';

    protected $fillable = [
        'year',
        'school',
        'talent',
    ];
}
