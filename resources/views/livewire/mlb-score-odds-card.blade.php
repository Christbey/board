<div class="bg-white shadow-md rounded-lg p-4 mb-4">
    <div class="flex justify-between items-center">
        <div>
            <p class="text-lg font-bold">{{ $score->homeTeam->name }}</p>
            <p class="text-sm">{{ $score->home_team_score ?? '-' }}</p>
        </div>
        <div>
            <p class="text-lg font-bold">{{ $score->awayTeam->name }}</p>
            <p class="text-sm">{{ $score->away_team_score ?? '-' }}</p>
        </div>
    </div>
    <div class="mt-4">
        <p class="text-sm font-semibold">Odds:</p>
        <ul class="text-sm">
            <li>H2H Home: {{ \App\Helpers\FormatHelper::formatOdds($odd->h2h_home_price) }}</li>
            <li>H2H Away: {{ \App\Helpers\FormatHelper::formatOdds($odd->h2h_away_price) }}</li>
            <li>Spread Home: {{ \App\Helpers\FormatHelper::formatOdds($odd->spread_home_price) }}</li>
            <li>Spread Away: {{ \App\Helpers\FormatHelper::formatOdds($odd->spread_away_price) }}</li>
            <li>Total Over: {{ \App\Helpers\FormatHelper::formatOdds($odd->total_over_price) }}</li>
            <li>Total Under: {{ \App\Helpers\FormatHelper::formatOdds($odd->total_under_price) }}</li>
        </ul>
    </div>
</div>
