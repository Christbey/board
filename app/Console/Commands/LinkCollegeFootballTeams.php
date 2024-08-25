<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballTeam;
use App\Models\NcaaTeam;

class LinkCollegeFootballTeams extends Command
{
    protected $signature = 'link:college-football-teams';
    protected $description = 'Link NCAA Teams with College Football Teams based on manually linked cf_team_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Clear existing links in the CollegeFootballTeam table
        CollegeFootballTeam::query()->update(['ncaa_team_id' => null]);

        // Fetch all NCAA teams that have a linked cf_team_id
        $ncaaTeams = NcaaTeam::whereNotNull('cf_team_id')->get();

        foreach ($ncaaTeams as $ncaaTeam) {
            // Find the CollegeFootballTeam by cf_team_id
            $collegeTeam = CollegeFootballTeam::find($ncaaTeam->cf_team_id);

            if ($collegeTeam) {
                // Update the CollegeFootballTeam's NCAA ID with the current NCAA team ID
                $collegeTeam->ncaa_team_id = $ncaaTeam->id;
                $collegeTeam->save();

                $this->info("Linked College Football Team ID {$collegeTeam->id} with NCAA Team ID {$ncaaTeam->id}.");
            } else {
                $this->warn("No College Football Team found for NCAA Team ID {$ncaaTeam->id}.");
            }
        }

        $this->info('Linking process completed.');
        return 0;
    }
}
