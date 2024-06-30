<!DOCTYPE html>
<html>
<head>
    <title>Predictions</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

<h1>Predictions</h1>

@if(isset($message))
    <p>{{ $message }}</p>
@else
    @if(!empty($predictions))
        <table>
            <thead>
            <tr>
                <th>Game ID</th>
                <th>Home Team</th>
                <th>Away Team</th>
                <th>Predicted Winner</th>
                <th>Predicted Home Points</th>
                <th>Predicted Away Points</th>
                <th>Home QBR</th>
                <th>Away QBR</th>
            </tr>
            </thead>
            <tbody>
            @foreach($predictions as $prediction)
                <tr>
                    <td>{{ $prediction['game_id'] }}</td>
                    <td>{{ $prediction['home_team_name'] }}</td>
                    <td>{{ $prediction['away_team_name'] }}</td>
                    <td>{{ $prediction['predicted_winner'] }}</td>
                    <td>{{ $prediction['home_pts'] }}</td>
                    <td>{{ $prediction['away_pts'] }}</td>
                    <td>{{ $prediction['home_qbr'] }}</td>
                    <td>{{ $prediction['away_qbr'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <h2>Win Counts</h2>
        <table>
            <thead>
            <tr>
                <th>Team Name</th>
                <th>Win Count</th>
            </tr>
            </thead>
            <tbody>
            @foreach($winCountsWithNames as $winCount)
                <tr>
                    <td>{{ $winCount['team_name'] }}</td>
                    <td>{{ $winCount['win_count'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No predictions available.</p>
    @endif
@endif

</body>
</html>
