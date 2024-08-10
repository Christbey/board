<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeFootballConference extends Model
{
    use HasFactory;

    protected $table = 'college_football_conferences';

    protected $fillable = [
        'abbreviation',
        'name',
        'short_name',
        'classification', // Correct field name
    ];
}
