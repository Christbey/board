<x-app-layout>
    <div class="container">
        @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @else
            <h1>Prediction for {{ $prediction['home_team'] }} vs {{ $prediction['away_team'] }}</h1>
            <p><strong>Predicted Winner:</strong> {{ $prediction['predicted_winner'] }}</p>
            <p><strong>Home FPI:</strong> {{ $prediction['home_fpi'] }}</p>
            <p><strong>Away FPI:</strong> {{ $prediction['away_fpi'] }}</p>
            <p><strong>Home Elo:</strong> {{ $prediction['home_elo'] }}</p>
            <p><strong>Away Elo:</strong> {{ $prediction['away_elo'] }}</p>
            <p><strong>Spread:</strong> {{ $prediction['spread'] }}</p>
            <p><strong>Home Win Probability:</strong> {{ $prediction['home_win_prob'] }}%</p>
            <p><strong>Home Score:</strong> {{ $prediction['home_score'] }}</p>
            <p><strong>Away Score:</strong> {{ $prediction['away_score'] }}</p>
        @endif
    </div>
</x-app-layout>