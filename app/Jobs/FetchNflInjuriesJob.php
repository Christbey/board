<?php

namespace App\Jobs;

use App\Models\NflEspnInjury;
use App\Models\NflEspnTeam;
use App\Models\NflEspnAthlete;
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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5; // Number of attempts
    public $backoff = [10, 30, 60]; // Retry intervals in seconds

    protected $teamId;

    /**
     * Create a new job instance.
     *
     * @param int|null $teamId
     * @return void
     */
    public function __construct($teamId = null)
    {
        $this->teamId = $teamId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $teams = $this->teamId ? NflEspnTeam::where('team_id', $this->teamId)->get() : NflEspnTeam::all();

        foreach ($teams as $team) {
            $lockKey = 'fetch_injuries_team_' . $team->team_id;

            if (Cache::lock($lockKey, 600)->get()) { // 600 seconds = 10 minutes lock
                try {
                    Log::info("Fetching injuries for team: {$team->display_name}");

                    $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/teams/{$team->team_id}/injuries";
                    $response = Http::get($url);

                    if ($response->successful()) {
                        $injuries = $response->json()['items'];

                        foreach ($injuries as $injuryRef) {
                            $injuryUrl = $injuryRef['$ref'];
                            $injuryResponse = Http::get($injuryUrl);

                            if ($injuryResponse->successful()) {
                                $injury = $injuryResponse->json();

                                // Fetch athlete details from the $ref link
                                $athleteUrl = $injury['athlete']['$ref'];
                                $athleteResponse = Http::get($athleteUrl);
                                $athleteId = null;
                                $seasonYear = date('Y'); // Get the current year

                                if ($athleteResponse->successful()) {
                                    $athlete = $athleteResponse->json();
                                    $athleteId = $athlete['id'];

                                    // Ensure the athlete exists in the nfl_espn_athletes table
                                    NflEspnAthlete::updateOrCreate(
                                        ['athlete_id' => $athlete['id']],
                                        [
                                            'full_name' => $athlete['fullName'],
                                            'team_id' => $team->team_id,
                                            'season_year' => $seasonYear // Add the season year
                                        ]
                                    );
                                }

                                // Truncate description if necessary
                                $description = $injury['shortComment'] ?? null;
                                if ($description && strlen($description) > 255) {
                                    $description = substr($description, 0, 255);
                                }

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
                            } else {
                                Log::error("Failed to fetch data for injury URL: $injuryUrl for team: {$team->display_name}");
                            }
                        }

                        Log::info("Injuries data for team {$team->display_name} has been fetched and stored successfully.");
                    } else {
                        Log::error("Failed to fetch injuries data for team {$team->display_name}. Response: {$response->body()}");
                    }

                    // Sleep for 5 seconds to avoid rate limiting
                    sleep(5);
                } catch (RequestException $e) {
                    Log::error("Request failed for team {$team->display_name}: " . $e->getMessage());
                    $this->release(30); // Retry the job after 30 seconds
                } catch (Exception $e) {
                    Log::error("General error for team {$team->display_name}: " . $e->getMessage());
                    $this->fail($e); // Mark the job as failed
                } finally {
                    Cache::lock($lockKey)->release(); // Release the lock
                }
            } else {
                Log::info("Job for team {$team->display_name} is already being processed.");
            }
        }
    }
}
