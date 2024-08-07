<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnEventPredictorsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_event_predictors', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->primary();
            $table->string('name');
            $table->string('short_name');
            $table->timestamp('last_modified');
            $table->unsignedBigInteger('home_team_id');
            $table->unsignedBigInteger('away_team_id');

            // Add columns for each statistic
            $table->float('home_gameProjection')->nullable();
            $table->float('home_matchupQuality')->nullable();
            $table->float('home_oppSeasonStrengthFbsRank')->nullable();
            $table->float('home_oppSeasonStrengthRating')->nullable();
            $table->float('home_teamAvgWp')->nullable();
            $table->float('home_teamChanceLoss')->nullable();
            $table->float('home_teamChanceTie')->nullable();
            $table->float('home_teamDefEff')->nullable();
            $table->float('home_teamOffEff')->nullable();
            $table->float('home_teamPredPtDiff')->nullable();
            $table->float('home_teamSTEff')->nullable();
            $table->float('home_teamTotEff')->nullable();

            $table->float('away_gameProjection')->nullable();
            $table->float('away_matchupQuality')->nullable();
            $table->float('away_oppSeasonStrengthFbsRank')->nullable();
            $table->float('away_oppSeasonStrengthRating')->nullable();
            $table->float('away_teamAvgWp')->nullable();
            $table->float('away_teamChanceLoss')->nullable();
            $table->float('away_teamChanceTie')->nullable();
            $table->float('away_teamDefEff')->nullable();
            $table->float('away_teamOffEff')->nullable();
            $table->float('away_teamPredPtDiff')->nullable();
            $table->float('away_teamSTEff')->nullable();
            $table->float('away_teamTotEff')->nullable();

            $table->timestamps();

            // Add foreign key constraints
            $table->foreign('home_team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
            $table->foreign('away_team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('nfl_espn_event_predictors', function (Blueprint $table) {
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
        });

        Schema::dropIfExists('nfl_espn_event_predictors');
    }
}
