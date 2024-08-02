<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NflEspnEvent;
use App\Models\NflEspnWeek;

class EspnEventController extends Controller
{
    public function index(Request $request)
    {
        $weekId = $request->input('week_id');
        $weeks = NflEspnWeek::all();

        if ($weekId) {
            $events = NflEspnEvent::where('week_id', $weekId)->get();
        } else {
            $events = NflEspnEvent::all();
        }

        return view('espn.events', compact('events', 'weeks', 'weekId'));
    }

    public function filter(Request $request)
    {
        $weekId = $request->input('week_id');
        return redirect()->route('espn.events', ['week_id' => $weekId]);
    }
}
