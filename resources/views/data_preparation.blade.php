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

<form method="GET" action="{{ route('fetchData') }}">
    <label for="season_year">Season Year:</label>
    <input type="number" id="season_year" name="season_year" value="{{ request('season_year', 2023) }}">

    <label for="sos_method">SOS Method:</label>
    <select id="sos_method" name="sos_method">
        <option value="schedule" {{ request('sos_method', 'schedule') === 'schedule' ? 'selected' : '' }}>Schedule
        </option>
        <option value="power_rankings" {{ request('sos_method', 'schedule') === 'power_rankings' ? 'selected' : '' }}>
            Power Rankings
        </option>
    </select>

    <button type="submit">Submit</button>
</form>

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
                <th>Home SOS</th>
                <th>Away SOS</th>
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
                    <td>{{ $strengthOfSchedule[$prediction['home_team_id']] }}</td>
                    <td>{{ $strengthOfSchedule[$prediction['away_team_id']] }}</td>
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
<h2>Strength of Schedule</h2>
<table>
    <thead>
    <tr>
        <th>Team Name</th>
        <th>Strength of Schedule</th>
    </tr>
    </thead>
    <tbody>
    @foreach($strengthOfSchedule as $teamId => $sos)
        <tr>
            <td>{{ $teamNames[$teamId] }}</td>
            <td>{{ $sos }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Home Teams Covering Spread</h2>
<p>{{ $homeTeamsCoverSpreadCount }}</p>

</body>
</html>
