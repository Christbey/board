<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NflPlayer;
use App\Models\NflTeam;

class AssignTeamIdToPlayers extends Command
{
    protected $signature = 'assign:team-id';
    protected $description = 'Assign team_id to nfl_players based on the team column';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $teams = NflTeam::all()->pluck('id', 'abbreviation');

        NflPlayer::all()->each(function ($player) use ($teams) {
            if (isset($teams[$player->team])) {
                $player->team_id = $teams[$player->team];
                $player->save();
            } else {
                $this->error("Team not found for player: {$player->longName} ({$player->team})");
            }
        });

        $this->info('Team IDs assigned successfully.');
    }
}
