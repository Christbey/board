<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class FetchStadiumWeather extends Command
{
    protected $signature = '{year}';
    protected $description = 'Fetch historical weather data for NFL games from the Open-Meteo API';

    private $client;
    private $dateRanges = [
        '2022' => ['start' => '2022-08-01', 'end' => '2023-02-21'],
        '2023' => ['start' => '2023-08-01', 'end' => '2024-02-21'],
        '2024' => ['start' => '2024-08-01', 'end' => '2025-02-21'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    public function handle()
    {
        $year = $this->argument('year');
        if (!array_key_exists($year, $this->dateRanges)) {
            $this->error('Invalid year provided. Use 2022, 2023, or 2024.');
            return 1;
        }

        $this->fetchHistoricalWeather($year);
    }

    private function fetchHistoricalWeather($year): void
    {
        $range = $this->dateRanges[$year];
        $startDate = $range['start'];
        $endDate = $range['end'];

        $stadiums = DB::table('nfl_stadiums')->get()->keyBy('team_id');
        $gamesQuery = DB::table('nfl_team_schedules')
            ->whereBetween('game_date', [$startDate, $endDate]);

        $gamesQuery->chunkById(100, function ($games) use ($stadiums) {
            foreach ($games as $game) {
                if ($this->weatherDataExists($game->id)) {
                    $this->info("Weather data already exists for game ID {$game->id}, skipping API call.");
                    continue;
                }

                if (!isset($stadiums[$game->team_id_home])) {
                    $this->error("No stadium found for team ID {$game->team_id_home}");
                    continue;
                }

                $this->fetchAndStoreWeatherData($stadiums[$game->team_id_home], $game);
            }
        });
    }

    private function weatherDataExists($gameId): bool
    {
        return DB::table('nfl_weather')->where('game_id', $gameId)->exists();
    }

    private function fetchAndStoreWeatherData($stadium, $game): void
    {
        $gameDate = Carbon::parse($game->game_date)->format('Y-m-d');
        $gameTimeString = $game->game_time;

        $this->info("Fetching weather data for stadium: {$stadium->stadium_name} on {$gameDate} at {$gameTimeString}");

        try {
            $response = $this->client->get('https://archive-api.open-meteo.com/v1/era5', [
                'query' => [
                    'latitude' => $stadium->latitude,
                    'longitude' => $stadium->longitude,
                    'temperature_unit' => 'fahrenheit',
                    'timezone' => 'America/Chicago',
                    'start_date' => $gameDate,
                    'end_date' => $gameDate,
                    'hourly' => 'temperature_2m,windspeed_10m',
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if ($this->hasValidWeatherData($data)) {
                $this->storeWeatherData($stadium, $game, $data, $gameDate, $gameTimeString);
            } else {
                $this->error("No valid temperature or wind speed data available for {$stadium->stadium_name} on {$gameDate} at {$gameTimeString}");
            }
        } catch (RequestException $e) {
            $this->handleRequestException($e, $stadium->stadium_name);
        } catch (Exception $e) {
            $this->error("Failed to fetch weather data for {$stadium->stadium_name}: " . $e->getMessage());
        }
    }

    private function hasValidWeatherData($data): bool
    {
        return !empty($data['hourly']['temperature_2m']) && !empty($data['hourly']['windspeed_10m']) && !empty($data['hourly']['time']);
    }

    private function storeWeatherData($stadium, $game, $data, $gameDate, $gameTimeString): void
    {
        $gameTime = Carbon::parse("{$gameDate} {$gameTimeString}");
        $closestIndex = $this->getClosestWeatherIndex($data['hourly']['time'], $gameTime);

        if ($closestIndex !== null && Carbon::parse($data['hourly']['time'][$closestIndex])->isSameDay($gameTime)) {
            $temperature = $data['hourly']['temperature_2m'][$closestIndex];
            $windSpeed = $data['hourly']['windspeed_10m'][$closestIndex];

            $this->info("Game Date: $gameDate, Game Time: $gameTimeString, Closest Time: {$data['hourly']['time'][$closestIndex]}, Temperature: $temperature °F, Wind Speed: $windSpeed m/s");

            DB::table('nfl_weather')->updateOrInsert(
                ['game_id' => $game->id],
                [
                    'stadium_id' => $stadium->id,
                    'date' => $gameDate,
                    'game_time' => $gameTimeString,
                    'temp' => json_encode($temperature),
                    'wind' => $windSpeed,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $this->info("Stored weather data for {$stadium->stadium_name} on {$gameDate} at {$gameTimeString}");
        } else {
            $this->error("No valid temperature or wind speed data available for {$stadium->stadium_name} on {$gameDate} at {$gameTimeString}");
        }
    }

    private function getClosestWeatherIndex($timeArray, $gameTime): ?int
    {
        $closestIndex = null;
        $closestTimeDiff = null;

        foreach ($timeArray as $index => $time) {
            $timeCarbon = Carbon::parse($time);
            $timeDiff = $gameTime->diffInMinutes($timeCarbon);

            if ($closestTimeDiff === null || $timeDiff < $closestTimeDiff) {
                $closestTimeDiff = $timeDiff;
                $closestIndex = $index;
            }
        }

        return $closestIndex;
    }

    private function handleRequestException(RequestException $e, $stadiumName): void
    {
        $response = $e->getResponse();
        $responseBody = $response ? $response->getBody()->getContents() : 'No response body';
        $this->error("Failed to fetch weather data for {$stadiumName}: " . $e->getMessage());
        $this->error('Response: ' . $responseBody);
    }
}
