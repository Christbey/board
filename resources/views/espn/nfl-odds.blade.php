@php use Carbon\Carbon; @endphp
        <!DOCTYPE html>
<html>
<head>
    <title>NFL Odds</title>
</head>
<body>
<h1>NFL Odds</h1>
@if(!empty($oddsData))
    <table border="1">
        <thead>
        <tr>
            <th>Date</th>
            <th>Spread</th>
            <th>Over Odds</th>
            <th>Under Odds</th>
            <th>Total Line</th>
            <th>Total Result</th>
            <th>Money Line Odds</th>
            <th>Moneyline Winner</th>
            <th>Spread Odds</th>
            <th>Spread Winner</th>
            <th>Past Competition</th>
        </tr>
        </thead>
        <tbody>
        @foreach($oddsData['items'] as $item)
            <tr>
                <td>{{ Carbon::parse($item['lineDate'])->format('Y-m-d') }}</td>
                <td>{{ $item['spread'] }}</td>
                <td>{{ $item['overOdds'] }}</td>
                <td>{{ $item['underOdds'] }}</td>
                <td>{{ $item['totalLine'] }}</td>
                <td>{{ $item['totalResult'] }}</td>
                <td>{{ $item['moneyLineOdds'] }}</td>
                <td>{{ $item['moneylineWinner'] ? 'Yes' : 'No' }}</td>
                <td>{{ $item['spreadOdds'] }}</td>
                <td>{{ $item['spreadWinner'] ? 'Yes' : 'No' }}</td>
                <td>
                    @if(isset($item['pastCompetitionData']))
                        @foreach($item['pastCompetitionData']['competitors'] as $competitor)
                            <strong>{{ $competitor['teamData']['displayName'] }}</strong>
                            ({{ $competitor['homeAway'] }})<br>
                        @endforeach
                    @else
                        <p>No competition data</p>
                    @endif
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
