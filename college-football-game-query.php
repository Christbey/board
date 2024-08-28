<?php
return CollegeFootballGame::join(
  "college_football_pregame",
  "college_football_games.id",
  "=",
  "college_football_pregame.game_id"
)
  ->join("college_football_fpi_ratings as home_fpi", function ($join) {
    $join
      ->on("college_football_games.home_team_id", "=", "home_fpi.team_id")
      ->where("home_fpi.year", "=", 2024);
  })
  ->join("college_football_fpi_ratings as away_fpi", function ($join) {
    $join
      ->on("college_football_games.away_team_id", "=", "away_fpi.team_id")
      ->where("away_fpi.year", "=", 2024);
  })
  ->where("college_football_games.season", 2024)
  ->where("college_football_games.season_type", "regular")
  ->where("college_football_games.week", 1)
  ->where("college_football_games.home_division", "fbs")
  ->where("college_football_games.completed", 0)
  ->select(
    "college_football_games.id",
    "college_football_games.season",
    "college_football_games.week",
    "college_football_games.start_date",
    "college_football_games.venue",
    "college_football_games.home_team",
    "college_football_games.away_team",
    "college_football_games.home_pregame_elo",
    "college_football_games.away_pregame_elo",
    "college_football_pregame.spread", // Adjusted to fetch from the correct table
    "college_football_pregame.home_win_prob", // Adjusted to fetch from the correct table
    \DB::raw("MAX(home_fpi.fpi) as home_fpi_rating"),
    \DB::raw("MAX(away_fpi.fpi) as away_fpi_rating")
  )
  ->groupBy("college_football_games.id") // Group by game ID
  ->get();
