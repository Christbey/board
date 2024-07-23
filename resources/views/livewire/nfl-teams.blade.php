<div>
    <h1>NFL Teams</h1>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Team Name</th>
            <th>Expected Wins</th>
        </tr>
        </thead>
        <tbody>
        @foreach($teams as $team)
            <tr>
                <td>{{ $team->name }}</td>
                <td>{{ $expectedWins[$team->id] ?? 0 }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
