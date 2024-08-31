@php
    // Ensure Carbon is fully qualified
    use Carbon\Carbon;$currentDate = Carbon::now();

    // Define the NFL weeks with their start and end dates
    $weeks = config('nfl.weeks');

    // Determine the current week number
    $currentWeek = null;
    foreach ($weeks as $weekNumber => $dates) {
        if ($currentDate->between(Carbon::parse($dates['start']), Carbon::parse($dates['end']))) {
            $currentWeek = $weekNumber;
            break;
        }
    }
@endphp
<x-app-layout>
    <div class="relative flex items-center justify-center min-h-screen bg-gradient-to-tr from-pink-300/50 to-indigo-400/50">
        <div class="relative mx-auto max-w-2xl p-6 bg-white shadow-lg rounded-lg">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-left tracking-tight text-gray-900 sm:text-4xl">
                    Elevate your sports experience with Picksports
                </h1>

                <ul class="mt-6 text-lg text-left leading-8 text-gray-600 list-none space-y-4">
                    <li>🏈 <strong>Join the Fun:</strong> Bring Picksports to your office for some friendly competition
                        this NFL season.
                    </li>
                    <li>🗓️ <strong>Weekly Participation:</strong> Select the winners of NFL games every week and compete
                        against your colleagues.
                    </li>
                    <li>⏰ <strong>Submission Deadline:</strong> Make your picks before they lock 1 hour before kickoff
                        on Thursday night!
                    </li>
                    <li>📊 <strong>Results:</strong> Get results after the conclusion of Monday Night Football (MNF) and
                        see who’s on top.
                    </li>
                    <li>🏆 <strong>Prove Your Expertise:</strong> It’s time to show who’s the ultimate NFL guru in your
                        office!
                    </li>
                </ul>


                <div class="mt-8 flex justify-center gap-x-6">
                    <a href="{{ url('/nfl/picks/' . $currentWeek) }}"
                       class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow
       hover:bg-indigo-500">
                        Submit Weekly Picks
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
