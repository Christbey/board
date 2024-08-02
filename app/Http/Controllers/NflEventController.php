<?php

// app/Http/Controllers/NflEventController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NflEspnEvent;

class NflEventController extends Controller
{
    public function index()
    {
        $events = NflEspnEvent::all();
        return view('nfl.events', compact('events'));
    }
}
