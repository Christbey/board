<x-app-layout>
    @if (!empty($scores))
        <h1>MLB Scores</h1>
        <table>
            <thead>
            <tr>
                <th>Game</th>
                <th>Home Team</th>
                <th>Away Team</th>
                <th>Home Score</th>
                <th>Away Score</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($scores as $score)
                <tr>
                    <td>{{ $score['id'] ?? '' }}</td>
                    <td>{{ $score['home_team'] ?? '' }}</td>
                    <td>{{ $score['away_team'] ?? '' }}</td>
                    <td>{{ $score['scores'][0]['score'] ?? '' }}</td>
                    <td>{{ $score['scores'][1]['score'] ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No MLB scores available.</p>
    @endif

</x-app-layout>