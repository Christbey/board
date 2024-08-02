<!-- resources/views/espn/events.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>NFL ESPN Events</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">NFL ESPN Events</h1>

    <form method="GET" action="{{ route('espn.events') }}">
        <div class="form-group">
            <label for="week_id">Select Week:</label>
            <select name="week_id" id="week_id" class="form-control">
                <option value="">All Weeks</option>
                @foreach($weeks as $week)
                    <option value="{{ $week->id }}" {{ $week->id == $weekId ? 'selected' : '' }}>
                        Season {{ $week->season_year }} - Week {{ $week->week_number }} (Type: {{ $week->season_type }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-bordered mt-4">
        <thead>
        <tr>
            <th>ID</th>
            <th>UID</th>
            <th>Date</th>
            <th>Name</th>
            <th>Short Name</th>
            <th>Home Team ID</th>
            <th>Home Team Score</th>
            <th>Away Team ID</th>
            <th>Away Team Score</th>
            <th>Venue Name</th>
            <!-- Add more columns as needed -->
        </tr>
        </thead>
        <tbody>
        @foreach($events as $event)
            <tr>
                <td>{{ $event->id }}</td>
                <td>{{ $event->uid }}</td>
                <td>{{ $event->date }}</td>
                <td>{{ $event->name }}</td>
                <td>{{ $event->short_name }}</td>
                <td>{{ $event->home_team_id }}</td>
                <td>{{ $event->home_team_score }}</td>
                <td>{{ $event->away_team_id }}</td>
                <td>{{ $event->away_team_score }}</td>
                <td>{{ $event->venue_name }}</td>
                <!-- Add more columns as needed -->
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
