<?php

namespace App\Console\Commands\Espn\Athletes;

use App\Models\NflEspnSplit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AthleteSplits extends Command
{
    protected $signature = 'espn:nfl-splits {athlete_id}';
    protected $description = 'Fetch NFL splits from ESPN API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $athleteId = $this->argument('athlete_id');
        $url = "https://site.web.api.espn.com/apis/common/v3/sports/football/nfl/athletes/{$athleteId}/splits";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['splitCategories']) && count($data['splitCategories']) > 0) {
                foreach ($data['splitCategories'] as $category) {
                    $splitCategory = $category['name'];
                    if (isset($category['splits']) && count($category['splits']) > 0) {
                        foreach ($category['splits'] as $split) {
                            $splitType = $split['abbreviation'];
                            $displayName = $split['displayName'];
                            $stats = $split['stats'];

                            $splitData = [
                                'athlete_id' => $athleteId,
                                'split_category' => $splitCategory,
                                'split_type' => $splitType,
                                'display_name' => $displayName,
                            ];

                            // Map stats to corresponding labels
                            $labels = [
                                'TOT', 'SOLO', 'AST', 'SACK', 'STF', 'STFYDS', 'FF', 'FR', 'KB',
                                'INT', 'YDS', 'AVG', 'TD', 'LNG', 'PD'
                            ];
                            foreach ($labels as $index => $label) {
                                $splitData[$label] = $stats[$index] ?? null;
                            }

                            NflEspnSplit::updateOrCreate(
                                [
                                    'athlete_id' => $athleteId,
                                    'split_category' => $splitCategory,
                                    'split_type' => $splitType,
                                ],
                                $splitData
                            );
                        }
                    }
                }

                $this->info("NFL splits for athlete {$athleteId} fetched and stored successfully.");
            } else {
                $this->error("No splits data found for athlete {$athleteId}.");
            }
        } else {
            $this->error("Failed to fetch NFL splits for athlete {$athleteId}.");
        }
    }
}
