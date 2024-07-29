<x-app-layout>
    <body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-4xl font-bold mb-6">ESPN NFL Team Details</h1>

        @if(isset($teamData['displayName']))
            <livewire:team-details :teamData="$teamData"/>

            <h3 class="text-2xl font-semibold mb-4">Injuries:</h3>
            <livewire:team-injuries :injuries="$teamData['injuries']['items'] ?? []"/>

            <h3 class="text-2xl font-semibold mb-4">Notes:</h3>
            <livewire:team-notes :notes="$teamData['notes']['items'] ?? []"/>

            <h3 class="text-2xl font-semibold mb-4">Against the Spread Records:</h3>
            <livewire:team-spread-records :records="$teamData['againstTheSpreadRecords']['items'] ?? []"/>

            <h3 class="text-2xl font-semibold mb-4">Events:</h3>
            <livewire:team-events :events="$teamData['events']['items'] ?? []"/>

            <h3 class="text-2xl font-semibold mb-4">Transactions:</h3>
            <livewire:team-transactions :transactions="$teamData['transactions']['items'] ?? []"/>

            <h3 class="text-2xl font-semibold mb-4">Coaches:</h3>
            <livewire:team-coaches :coaches="$teamData['coaches']['items'] ?? []"/>

            <h3 class="text-2xl font-semibold mb-4">Attendance:</h3>
            <livewire:team-attendance :attendance="$teamData['attendance']['items'] ?? []"/>
        @else
            <p>No team data available.</p>
        @endif
    </div>
    @livewireScripts
</x-app-layout>
