@php use Carbon\Carbon; @endphp
        <!DOCTYPE html>
<html>
<head>
    <title>NFL Scoreboard</title>
</head>
<body>
<h1>NFL Scoreboard</h1>
@if(!empty($scoreboardData))
    <table border="1">
        <thead>
        <tr>
            <th>Event Date</th>
            <th>Home Team</th>
            <th>Away Team</th>
            <th>Score</th>
        </tr>
        </thead>
        <tbody>
        @foreach($scoreboardData['events'] as $event)
            <tr>
                <td>{{ Carbon::parse($event['date'])->format('Y-m-d') }}</td>
                <td>{{ $event['competitions'][0]['competitors'][0]['team']['displayName'] }}</td>
                <td>{{ $event['competitions'][0]['competitors'][1]['team']['displayName'] }}</td>
                <td>
                    {{ $event['competitions'][0]['competitors'][0]['score'] }} -
                    {{ $event['competitions'][0]['competitors'][1]['score'] }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p>No data available.</p>
@endif
</body>
</html>
