<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class FetchStadiumWeather extends Command
{
    protected $signature = 'fetch:stadium-weather {year}';
    protected $description = 'Fetch historical weather data for NFL games from the Open-Meteo API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        if (!in_array($year, ['2022', '2023', '2024'])) {
            $this->error('Invalid year provided. Use 2022, 2023, or 2024.');
            return 1;
        }

        $this->fetchHistoricalWeather($year);
    }

    private function fetchHistoricalWeather($year)
    {
        $client = new Client();
        $dateRanges = [
            '2022' => ['start' => '2022-08-01', 'end' => '2023-02-21'],
            '2023' => ['start' => '2023-08-01', 'end' => '2024-02-21'],
            '2024' => ['start' => '2024-08-01', 'end' => '2025-02-21'],
        ];

        $range = $dateRanges[$year];
        $startDate = $range['start'];
        $endDate = $range['end'];

        // Preload all stadiums
        $stadiums = DB::table('nfl_stadiums')->get()->keyBy('team_id');

        // Get all games for the year within the date range
        $games = DB::table('nfl_team_schedules')
            ->whereBetween('game_date', [$startDate, $endDate])
            ->get();

        if ($games->isEmpty()) {
            $this->info("No games found for the year $year.");
            return;
        }

        foreach ($games as $game) {
            // Check if weather data already exists for the game
            $existingWeatherData = DB::table('nfl_weather')
                ->where('game_id', $game->id)
                ->exists();

            if ($existingWeatherData) {
                $this->info("Weather data already exists for game ID {$game->id}, skipping API call.");
                continue;
            }

            if (!isset($stadiums[$game->team_id_home])) {
                $this->error("No stadium found for team ID {$game->team_id_home}");
                continue;
            }

            $stadium = $stadiums[$game->team_id_home];
            $gameDate = Carbon::parse($game->game_date)->format('Y-m-d');
            $gameTimeString = $game->game_time;

            $this->info("Fetching weather data for stadium: {$stadium->stadium_name} on {$gameDate} at {$gameTimeString}");

            try {
                // Fetch weather data for the game
                $response = $client->get('https://archive-api.open-meteo.com/v1/era5', [
                    'query' => [
                        'latitude' => $stadium->latitude,
                        'longitude' => $stadium->longitude,
                        'temperature_unit' => 'fahrenheit',
                        'timezone' => 'America/Chicago',
                        'start_date' => $gameDate,
                        'end_date' => $gameDate,
                        'hourly' => 'temperature_2m',
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                // Debug: Log the raw response data
                $this->info("Raw response for {$stadium->stadium_name} on {$gameDate}: " . json_encode($data));

                if (empty($data['hourly']['temperature_2m']) || empty($data['hourly']['time'])) {
                    $this->error("No temperature data available for {$stadium->stadium_name} on {$gameDate}");
                    continue;
                }

                $timeArray = $data['hourly']['time'];
                $temperatureArray = $data['hourly']['temperature_2m'];

                $gameTime = Carbon::parse($gameDate . ' ' . $gameTimeString);
                $closestIndex = null;
                $closestTimeDiff = null;

                // Find the closest temperature data for the game time
                foreach ($timeArray as $index => $time) {
                    $timeCarbon = Carbon::parse($time);
                    $timeDiff = $gameTime->diffInMinutes($timeCarbon);

                    if ($closestTimeDiff === null || $timeDiff < $closestTimeDiff) {
                        $closestTimeDiff = $timeDiff;
                        $closestIndex = $index;
                    }
                }

                if ($closestIndex !== null && Carbon::parse($timeArray[$closestIndex])->isSameDay($gameTime)) {
                    $closestTemperature = $temperatureArray[$closestIndex];

                    // Debug: Log the temperature data
                    $this->info("Game Date: $gameDate, Game Time: $gameTimeString, Closest Time: {$timeArray[$closestIndex]}, Temperature: $closestTemperature °F");

                    // Store the temperature data in the nfl_weather table
                    DB::table('nfl_weather')->updateOrInsert(
                        ['game_id' => $game->id],
                        [
                            'stadium_id' => $stadium->id,
                            'date' => $gameDate,
                            'game_time' => $gameTimeString,
                            'temperature_data' => $closestTemperature,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    $this->info("Stored weather data for {$stadium->stadium_name} on {$gameDate} at {$gameTimeString}");
                } else {
                    $this->error("No valid temperature data available for {$stadium->stadium_name} on {$gameDate} at {$gameTimeString}");
                }
            } catch (RequestException $e) {
                $response = $e->getResponse();
                $responseBody = $response ? $response->getBody()->getContents() : 'No response body';
                $this->error("Failed to fetch weather data for {$stadium->stadium_name}: " . $e->getMessage());
                $this->error('Response: ' . $responseBody);
            } catch (\Exception $e) {
                $this->error("Failed to fetch weather data for {$stadium->stadium_name}: " . $e->getMessage());
            }
        }
    }
}
