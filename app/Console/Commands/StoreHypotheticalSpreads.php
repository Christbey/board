<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballGame;
use App\Models\CollegeHypotheticalSpread;
use App\Services\CollegeFootball\CollegeFootballPredictionService;
use Illuminate\Support\Facades\Log;

class StoreHypotheticalSpreads extends Command
{
    protected $signature = 'store:hypothetical-spreads';
    protected $description = 'Store hypothetical spreads for eligible games';

    protected $predictionService;

    public function __construct(CollegeFootballPredictionService $predictionService)
    {
        parent::__construct();
        $this->predictionService = $predictionService;
    }

    public function handle()
    {
        Log::info('Starting to process hypothetical spreads.');

        // Fetch all FBS games
        $games = CollegeFootballGame::where('home_division', 'fbs')
            ->where('season', 2024)
            ->get();

        if ($games->isEmpty()) {
            Log::warning('No games found to process.');
            return;
        }

        foreach ($games as $game) {
            Log::info("Processing game ID: {$game->id}");

            // Call the service method to get the prediction details
            $predictionDetails = $this->predictionService->getPredictionDetails($game->id);

            if (isset($predictionDetails['hypothetical_spread'])) {
                $hypotheticalSpread = $predictionDetails['hypothetical_spread'];
                $homeTeam = $predictionDetails['home_team'];

                $existingSpreadRecord = CollegeHypotheticalSpread::where('game_id', $game->id)->first();

                if (!$game->completed) {
                    Log::info("Game ID {$game->id} is not completed. Checking if we need to update or create a spread record.");
                    // Store or update the spread if the game is not completed
                    if ($hypotheticalSpread !== null && $hypotheticalSpread != 0) {
                        CollegeHypotheticalSpread::updateOrCreate(
                            ['game_id' => $game->id],
                            [
                                'home_team_school' => $homeTeam,
                                'spread' => $hypotheticalSpread,
                                'correct' => false // Placeholder, this will be updated after game completion
                            ]
                        );
                        Log::info("Spread record updated/created for game ID: {$game->id}");
                    } else {
                        Log::warning("Hypothetical spread is null or zero for game ID: {$game->id}. Skipping.");
                    }
                } else {
                    // If game is completed, update the 'correct' field only
                    Log::info("Game ID {$game->id} is completed. Checking correctness of the spread.");

                    if ($existingSpreadRecord) {
                        $actualDifference = $game->home_points - $game->away_points;
                        $predictedDifference = $hypotheticalSpread;

                        // Check if the prediction was correct
                        $correct = ($predictedDifference > 0 && $actualDifference >= $predictedDifference) ||
                            ($predictedDifference < 0 && $actualDifference <= $predictedDifference);

                        // Debugging output
                        Log::info("Updating correct field for game ID: {$game->id}");
                        Log::info("Actual difference: $actualDifference");
                        Log::info("Predicted difference: $predictedDifference");
                        Log::info('Correct: ' . ($correct ? 'true' : 'false'));

                        $existingSpreadRecord->update([
                            'correct' => $correct
                        ]);
                    } else {
                        Log::warning("No spread record found for completed game ID: {$game->id}");
                    }
                }
            } else {
                Log::warning("No hypothetical spread calculated for game ID: {$game->id}");
            }
        }

        Log::info('Hypothetical spreads processing completed.');
    }
}
