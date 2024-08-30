<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sagarin extends Model
{
    protected $table = 'sagarins';

    // Add 'id', 'team_name', and 'rating' to the fillable array
    protected $fillable = ['id', 'team_name', 'rating'];

    // Disable auto-incrementing for the id since it's being manually set
    public $incrementing = false;
}