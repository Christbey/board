<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Step 1: Get today's date
$today = Carbon::today();

// Step 2: Retrieve all games scheduled for today in the 2024 season
$todaysGames = DB::table("college_football_games")
  ->where("season", 2024)
  ->whereDate("start_date", $today)
  ->get();

// Step 3: Extract the team_ids for both home and away teams
$teamIds = $todaysGames
  ->pluck("home_team_id")
  ->merge($todaysGames->pluck("away_team_id"))
  ->unique();

// Step 4: For each team, find the last 6 games and calculate the average stats
$averageStats = collect();

foreach ($teamIds as $teamId) {
  $lastSixGames = DB::table("college_football_games")
    ->where(function ($query) use ($teamId) {
      $query->where("home_team_id", $teamId)->orWhere("away_team_id", $teamId);
    })
    ->where("season", 2023)
    ->orderBy("start_date", "desc")
    ->limit(6)
    ->get();

  // Calculate averages for the last 6 games
  $avgOffensePpa = DB::table("college_football_adv_game_stats")
    ->whereIn("game_id", $lastSixGames->pluck("id"))
    ->where("team_id", $teamId)
    ->avg("offense_ppa");

  $avgHomePoints = $lastSixGames
    ->where("home_team_id", $teamId)
    ->avg("home_points");
  $avgAwayPoints = $lastSixGames
    ->where("away_team_id", $teamId)
    ->avg("away_points");

  $avgPointsGivenUpAtHome = $lastSixGames
    ->where("home_team_id", $teamId)
    ->avg("away_points");
  $avgPointsGivenUpAway = $lastSixGames
    ->where("away_team_id", $teamId)
    ->avg("home_points");

  $teamName = DB::table("college_football_teams")
    ->where("id", $teamId)
    ->value("school");

  $averageStats->push(
    (object) [
      "team_id" => $teamId,
      "school" => $teamName,
      "avg_offense_ppa" => $avgOffensePpa,
      "avg_home_points" => $avgHomePoints,
      "avg_away_points" => $avgAwayPoints,
      "avg_points_given_up_at_home" => $avgPointsGivenUpAtHome,
      "avg_points_given_up_away" => $avgPointsGivenUpAway
    ]
  );
}

// Step 5: Display the results
foreach ($averageStats as $stat) {
  echo "Team: {$stat->school}, Average Offense PPA: {$stat->avg_offense_ppa}, Average Home Points Scored: {$stat->avg_home_points}, Average Away Points Scored: {$stat->avg_away_points}, Average Points Given Up at Home: {$stat->avg_points_given_up_at_home}, Average Points Given Up Away: {$stat->avg_points_given_up_away}" .
    PHP_EOL;
}
