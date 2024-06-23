<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;

class FetchStadiumWeather extends Command
{
    protected $signature = 'fetch:stadium-weather {year}';
    protected $description = 'Fetch historical weather data for NFL stadiums from the Open-Meteo API';

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

        $schedules = DB::table('nfl_team_schedules')
            ->whereBetween('game_date', [$startDate, $endDate])
            ->get();

        foreach ($schedules as $schedule) {
            $stadium = DB::table('nfl_stadiums')
                ->where('team_id', $schedule->team_id_home)
                ->first();

            if ($stadium) {
                $gameDate = Carbon::parse($schedule->game_date)->format('Y-m-d');

                try {
                    $response = $client->get('https://archive-api.open-meteo.com/v1/era5', [
                        'query' => [
                            'latitude' => $stadium->latitude,
                            'longitude' => $stadium->longitude,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'hourly' => 'temperature_2m'
                        ]
                    ]);

                    $data = json_decode($response->getBody(), true);

                    if (isset($data['hourly']['temperature_2m']) && isset($data['hourly']['time'])) {
                        $filteredData = [
                            'time' => [],
                            'temperature_2m' => [],
                        ];

                        foreach ($data['hourly']['time'] as $index => $time) {
                            $timeCarbon = Carbon::parse($time);
                            if ($timeCarbon->isSameDay($gameDate)) {
                                // Convert Celsius to Fahrenheit
                                $tempCelsius = $data['hourly']['temperature_2m'][$index];
                                $tempFahrenheit = ($tempCelsius * 9 / 5) + 32;

                                $filteredData['time'][] = $time;
                                $filteredData['temperature_2m'][] = $tempFahrenheit;
                            }
                        }

                        if (!empty($filteredData['time'])) {
                            // Use updateOrInsert to store or update the data in the nfl_weather table
                            DB::table('nfl_weather')->updateOrInsert(
                                ['game_id' => $schedule->id],
                                [
                                    'stadium_id' => $stadium->id,
                                    'date' => $gameDate,
                                    'temperature_data' => json_encode($filteredData),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );

                            $this->info("Fetched and stored historical weather data for {$stadium->stadium_name} on {$gameDate}");
                        } else {
                            $this->error("No temperature data available for {$stadium->stadium_name} on {$gameDate}");
                        }
                    } else {
                        $this->error("No temperature data available for {$stadium->stadium_name} on {$gameDate}");
                    }
                } catch (\Exception $e) {
                    $this->error("Failed to fetch weather data for {$stadium->stadium_name} on {$gameDate}: " . $e->getMessage());
                }
            } else {
                $this->error("No stadium found for team_id {$schedule->team_id_home}");
            }
        }
    }
}
