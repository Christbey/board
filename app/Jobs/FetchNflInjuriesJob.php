<?php

namespace App\Jobs;

use App\Models\NflEspnInjury;
use App\Models\NflEspnTeam;
use App\Models\NflEspnAthlete;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Http\Client\RequestException;

class FetchNflInjuriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected NflEspnTeam $team;

    public function __construct(NflEspnTeam $team)
    {
        $this->team = $team;
    }

    public function handle()
    {
        $team = $this->team;
        $cacheKey = "nfl_injuries_{$team->team_id}";
        $lockKey = "fetch_injuries_team_{$team->team_id}";

        // Attempt to acquire the lock
        if (!Cache::lock($lockKey, 600)->get()) {
            Log::info("Job for team {$team->display_name} is already being processed.");
            return;
        }

        try {
            Log::info("Fetching injuries for team: {$team->display_name}");

            // Check if the response is cached
            $injuries = Cache::remember($cacheKey, 600, function () use ($team) {
                $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/teams/{$team->team_id}/injuries";
                $response = Http::get($url);

                if (!$response->successful()) {
                    Log::error("Failed to fetch injuries data for team {$team->display_name}. Response: {$response->body()}");
                    return [];
                }

                return $response->json()['items'] ?? [];
            });

            if (empty($injuries)) {
                Log::info("No injuries data found for team {$team->display_name}.");
                return;
            }

            foreach ($injuries as $injuryRef) {
                $this->processInjury($team, $injuryRef['$ref']);
            }

            Log::info("Injuries data for team {$team->display_name} has been fetched and stored successfully.");

            // Clear the cache after processing
            Cache::forget($cacheKey);

            // Sleep to avoid rate limiting
            sleep(1);
        } catch (RequestException $e) {
            Log::error("Request failed for team {$team->display_name}: " . $e->getMessage());
            $this->release(30); // Retry the job after 30 seconds
        } catch (Exception $e) {
            Log::error("General error for team {$team->display_name}: " . $e->getMessage());
            $this->fail($e); // Mark the job as failed
        } finally {
            // Release the lock
            Cache::lock($lockKey)->release();
        }
    }

    protected function processInjury(NflEspnTeam $team, string $injuryUrl)
    {
        try {
            $injuryResponse = Http::get($injuryUrl);

            if (!$injuryResponse->successful()) {
                Log::error("Failed to fetch data for injury URL: $injuryUrl for team: {$team->display_name}");
                return;
            }

            $injury = $injuryResponse->json();
            $athleteId = $this->processAthlete($team, $injury['athlete']['$ref']);
            $this->storeInjury($team, $injury, $athleteId);
        } catch (Exception $e) {
            Log::error("Error processing injury for team {$team->display_name}: " . $e->getMessage());
        }
    }

    protected function processAthlete(NflEspnTeam $team, string $athleteUrl)
    {
        $athleteResponse = Http::get($athleteUrl);

        if (!$athleteResponse->successful()) {
            Log::error("Failed to fetch athlete data for team {$team->display_name}");
            return null;
        }

        $athlete = $athleteResponse->json();
        $seasonYear = date('Y');

        NflEspnAthlete::updateOrCreate(
            ['athlete_id' => $athlete['id']],
            [
                'full_name' => $athlete['fullName'],
                'team_id' => $team->team_id,
                'season_year' => $seasonYear,
            ]
        );

        return $athlete['id'];
    }

    protected function storeInjury(NflEspnTeam $team, array $injury, $athleteId)
    {
        $description = $injury['shortComment'] ?? null;
        $description = $description && strlen($description) > 255 ? substr($description, 0, 255) : $description;

        // Check if the injury already exists with the same team_id, athlete_id, and type
        $existingInjury = NflEspnInjury::where('team_id', $team->team_id)
            ->where('athlete_id', $athleteId)
            ->where('type', $injury['type']['description'] ?? null)
            ->first();

        if ($existingInjury) {
            Log::info("Injury for team {$team->display_name}, athlete {$athleteId}, and type {$injury['type']['description']} already exists. Skipping creation.");
            return;
        }

        // If not exists, create or update the injury record
        NflEspnInjury::updateOrCreate(
            ['injury_id' => $injury['id']],
            [
                'team_id' => $team->team_id,
                'athlete_id' => $athleteId,
                'type' => $injury['type']['description'] ?? null,
                'status' => $injury['status'] ?? null,
                'date' => isset($injury['date']) ? date('Y-m-d', strtotime($injury['date'])) : null,
                'description' => $description,
            ]
        );
    }
}
