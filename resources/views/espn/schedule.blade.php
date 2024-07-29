<!DOCTYPE html>
<html>
<head>
    <title>ESPN NFL Schedule</title>
    <style>
        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .team-info {
            display: flex;
            align-items: center;
        }

        .team-info img {
            margin-right: 10px;
            width: 50px; /* Adjust size as needed */
            height: auto;
        }
    </style>
</head>
<body>
<h1>ESPN NFL Schedule</h1>

@if(isset($scheduleData['events']))
    @foreach($scheduleData['events'] as $event)
        <h2>{{ $event['date'] }}</h2>
        <ul>
            <li>
                <div class="team-info">
                    <img src="{{ $event['competitions'][0]['competitors'][0]['team']['logos'][0]['href'] ?? '' }}"
                         alt="Home Team Logo">
                    <strong>Home
                        Team:</strong> {{ $event['competitions'][0]['competitors'][0]['team']['displayName'] ?? 'N/A' }}
                </div>
                <div class="team-info">
                    <img src="{{ $event['competitions'][0]['competitors'][1]['team']['logos'][0]['href'] ?? '' }}"
                         alt="Away Team Logo">
                    <strong>Away
                        Team:</strong> {{ $event['competitions'][0]['competitors'][1]['team']['displayName'] ?? 'N/A' }}
                </div>
                <strong>Venue:</strong> {{ $event['competitions'][0]['venue']['fullName'] ?? 'N/A' }}<br>
                <strong>Start Date:</strong> {{ $event['date'] ?? 'N/A' }}<br>
            </li>
        </ul>
    @endforeach
@else
    <p>No schedule data available.</p>
@endif
</body>
</html>
