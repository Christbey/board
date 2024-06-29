<!DOCTYPE html>
<html>
<head>
    <title>Matched Schedules with Odds</title>
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

<h1>Matched Schedules with Odds</h1>

<table>
    <thead>
    <tr>
        <th>Game ID</th>
        <th>Game Date</th>
        <th>Home Team</th>
        <th>Away Team</th>
        <th>Commence Time</th>
        <th>Odds</th>
    </tr>
    </thead>
    <tbody>
    @foreach($matchedData as $data)
        <tr>
            <td>{{ $data['schedule']->game_id }}</td>
            <td>{{ $data['schedule']->game_date }}</td>
            <td>{{ $data['schedule']->team_id_home }}</td>
            <td>{{ $data['schedule']->team_id_away }}</td>
            <td>{{ $data['odds']->commence_time }}</td>
            <td>
                @if($data['odds'])
                    {{ $data['odds']->h2h_home_price }} / {{ $data['odds']->h2h_away_price }}
                @else
                    No Odds Available
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
