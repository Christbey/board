<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEspnAthleteSplitsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_athlete_splits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('athlete_id')->nullable();
            $table->string('split_id')->nullable();
            $table->string('split_name')->nullable();
            $table->string('split_abbreviation')->nullable();
            $table->string('category_name')->nullable();
            $table->string('category_displayName')->nullable();
            $table->string('category_shortDisplayName')->nullable();
            $table->string('category_abbreviation')->nullable();
            $table->unsignedBigInteger('away_team_id')->nullable();
            $table->unsignedBigInteger('home_team_id')->nullable();
            // General stats
            $table->integer('general_fumbles')->nullable();
            $table->integer('general_fumblesLost')->nullable();
            $table->integer('general_fumblesForced')->nullable();
            $table->integer('general_fumblesRecovered')->nullable();
            $table->integer('general_fumblesRecoveredYards')->nullable();
            $table->integer('general_fumblesTouchdowns')->nullable();
            $table->integer('general_gamesPlayed')->nullable();
            $table->integer('general_offensiveTwoPtReturns')->nullable();
            $table->integer('general_offensiveFumblesTouchdowns')->nullable();
            $table->integer('general_defensiveFumblesTouchdowns')->nullable();

            // Passing stats
            $table->decimal('passing_avgGain', 5, 2)->nullable();
            $table->decimal('passing_completionPct', 5, 2)->nullable();
            $table->integer('passing_completions')->nullable();
            $table->decimal('passing_ESPNQBRating', 5, 2)->nullable();
            $table->decimal('passing_interceptionPct', 5, 2)->nullable();
            $table->integer('passing_interceptions')->nullable();
            $table->integer('passing_longPassing')->nullable();
            $table->integer('passing_miscYards')->nullable();
            $table->integer('passing_netPassingYards')->nullable();
            $table->decimal('passing_netPassingYardsPerGame', 5, 2)->nullable();
            $table->integer('passing_netTotalYards')->nullable();
            $table->decimal('passing_netYardsPerGame', 5, 2)->nullable();
            $table->integer('passing_attempts')->nullable();
            $table->integer('passing_bigPlays')->nullable();
            $table->integer('passing_firstDowns')->nullable();
            $table->integer('passing_fumbles')->nullable();
            $table->integer('passing_fumblesLost')->nullable();
            $table->decimal('passing_touchdownPct', 5, 2)->nullable();
            $table->integer('passing_touchdowns')->nullable();
            $table->integer('passing_yards')->nullable();
            $table->integer('passing_yardsAfterCatch')->nullable();
            $table->integer('passing_yardsAtCatch')->nullable();
            $table->decimal('passing_yardsPerGame', 5, 2)->nullable();
            $table->decimal('passing_rating', 5, 2)->nullable();
            $table->integer('passing_sacks')->nullable();
            $table->integer('passing_sackYardsLost')->nullable();
            $table->integer('passing_netAttempts')->nullable();
            $table->integer('passing_teamGamesPlayed')->nullable();
            $table->integer('passing_totalOffensivePlays')->nullable();
            $table->integer('passing_totalPoints')->nullable();
            $table->decimal('passing_totalPointsPerGame', 5, 2)->nullable();
            $table->integer('passing_totalTouchdowns')->nullable();
            $table->integer('passing_totalYards')->nullable();
            $table->integer('passing_totalYardsFromScrimmage')->nullable();
            $table->integer('passing_twoPtPass')->nullable();
            $table->integer('passing_twoPtPassAttempts')->nullable();
            $table->integer('passing_yardsFromScrimmagePerGame')->nullable();
            $table->decimal('passing_yardsPerCompletion', 5, 2)->nullable();
            $table->decimal('passing_yardsPerAttempt', 5, 2)->nullable();
            $table->decimal('passing_netYardsPerAttempt', 5, 2)->nullable();
            $table->decimal('passing_QBR', 5, 2)->nullable();
            $table->decimal('passing_adjQBR', 5, 2)->nullable();
            $table->decimal('passing_quarterbackRating', 5, 2)->nullable();

            // Rushing stats
            $table->decimal('rushing_avgGain', 5, 2)->nullable();
            $table->decimal('rushing_ESPNRBRating', 5, 2)->nullable();
            $table->integer('rushing_longRushing')->nullable();
            $table->integer('rushing_miscYards')->nullable();
            $table->integer('rushing_netTotalYards')->nullable();
            $table->decimal('rushing_netYardsPerGame', 5, 2)->nullable();
            $table->integer('rushing_attempts')->nullable();
            $table->integer('rushing_bigPlays')->nullable();
            $table->integer('rushing_firstDowns')->nullable();
            $table->integer('rushing_fumbles')->nullable();
            $table->integer('rushing_fumblesLost')->nullable();
            $table->integer('rushing_touchdowns')->nullable();
            $table->integer('rushing_yards')->nullable();
            $table->decimal('rushing_yardsPerGame', 5, 2)->nullable();
            $table->integer('rushing_stuffs')->nullable();
            $table->integer('rushing_stuffYardsLost')->nullable();
            $table->integer('rushing_teamGamesPlayed')->nullable();
            $table->integer('rushing_totalOffensivePlays')->nullable();
            $table->integer('rushing_totalPoints')->nullable();
            $table->decimal('rushing_totalPointsPerGame', 5, 2)->nullable();
            $table->integer('rushing_totalTouchdowns')->nullable();
            $table->integer('rushing_totalYards')->nullable();
            $table->integer('rushing_totalYardsFromScrimmage')->nullable();
            $table->integer('rushing_twoPtRush')->nullable();
            $table->integer('rushing_twoPtRushAttempts')->nullable();
            $table->integer('rushing_yardsFromScrimmagePerGame')->nullable();
            $table->decimal('rushing_yardsPerAttempt', 5, 2)->nullable();

            // Receiving stats
            $table->decimal('receiving_avgGain', 5, 2)->nullable();
            $table->decimal('receiving_ESPNWRRating', 5, 2)->nullable();
            $table->integer('receiving_longReception')->nullable();
            $table->integer('receiving_miscYards')->nullable();
            $table->integer('receiving_netTotalYards')->nullable();
            $table->decimal('receiving_netYardsPerGame', 5, 2)->nullable();
            $table->integer('receiving_bigPlays')->nullable();
            $table->integer('receiving_firstDowns')->nullable();
            $table->integer('receiving_fumbles')->nullable();
            $table->integer('receiving_fumblesLost')->nullable();
            $table->integer('receiving_targets')->nullable();
            $table->integer('receiving_touchdowns')->nullable();
            $table->integer('receiving_yards')->nullable();
            $table->integer('receiving_yardsAfterCatch')->nullable();
            $table->integer('receiving_yardsAtCatch')->nullable();
            $table->decimal('receiving_yardsPerGame', 5, 2)->nullable();
            $table->integer('receiving_receptions')->nullable();
            $table->integer('receiving_teamGamesPlayed')->nullable();
            $table->integer('receiving_totalOffensivePlays')->nullable();
            $table->integer('receiving_totalPoints')->nullable();
            $table->decimal('receiving_totalPointsPerGame', 5, 2)->nullable();
            $table->integer('receiving_totalTouchdowns')->nullable();
            $table->integer('receiving_totalYards')->nullable();
            $table->integer('receiving_totalYardsFromScrimmage')->nullable();
            $table->integer('receiving_twoPtReception')->nullable();
            $table->integer('receiving_twoPtReceptionAttempts')->nullable();
            $table->integer('receiving_yardsFromScrimmagePerGame')->nullable();
            $table->decimal('receiving_yardsPerReception', 5, 2)->nullable();

            // Defensive stats
            $table->integer('defensive_assistTackles')->nullable();
            $table->decimal('defensive_avgInterceptionYards', 5, 2)->nullable();
            $table->decimal('defensive_avgSackYards', 5, 2)->nullable();
            $table->decimal('defensive_avgStuffYards', 5, 2)->nullable();
            $table->integer('defensive_blockedFieldGoalTouchdowns')->nullable();
            $table->integer('defensive_blockedPuntTouchdowns')->nullable();
            $table->integer('defensive_touchdowns')->nullable();
            $table->integer('defensive_hurries')->nullable();
            $table->integer('defensive_kicksBlocked')->nullable();
            $table->integer('defensive_longInterception')->nullable();
            $table->integer('defensive_miscTouchdowns')->nullable();
            $table->integer('defensive_passesBattedDown')->nullable();
            $table->integer('defensive_passesDefended')->nullable();
            $table->integer('defensive_QBHits')->nullable();
            $table->integer('defensive_twoPtReturns')->nullable();
            $table->integer('defensive_sacks')->nullable();
            $table->integer('defensive_sacksAssisted')->nullable();
            $table->integer('defensive_sacksUnassisted')->nullable();
            $table->integer('defensive_sackYards')->nullable();
            $table->integer('defensive_safeties')->nullable();
            $table->integer('defensive_soloTackles')->nullable();
            $table->integer('defensive_stuffs')->nullable();
            $table->integer('defensive_stuffYards')->nullable();
            $table->integer('defensive_tacklesForLoss')->nullable();
            $table->integer('defensive_tacklesYardsLost')->nullable();
            $table->integer('defensive_teamGamesPlayed')->nullable();
            $table->integer('defensive_totalTackles')->nullable();
            $table->integer('defensive_yardsAllowed')->nullable();
            $table->integer('defensive_pointsAllowed')->nullable();
            $table->integer('defensive_onePtSafetiesMade')->nullable();

            // Defensive Interceptions stats
            $table->integer('defensiveInterceptions_interceptions')->nullable();
            $table->integer('defensiveInterceptions_interceptionTouchdowns')->nullable();
            $table->integer('defensiveInterceptions_interceptionYards')->nullable();

            // Kicking stats
            $table->decimal('kicking_avgKickoffReturnYards', 5, 2)->nullable();
            $table->decimal('kicking_avgKickoffYards', 5, 2)->nullable();
            $table->integer('kicking_extraPointAttempts')->nullable();
            $table->decimal('kicking_extraPointPct', 5, 2)->nullable();
            $table->integer('kicking_extraPointsBlocked')->nullable();
            $table->decimal('kicking_extraPointsBlockedPct', 5, 2)->nullable();
            $table->integer('kicking_extraPointsMade')->nullable();
            $table->integer('kicking_fairCatches')->nullable();
            $table->decimal('kicking_fairCatchPct', 5, 2)->nullable();
            $table->integer('kicking_fieldGoalAttempts')->nullable();
            $table->integer('kicking_fieldGoalAttempts1_19')->nullable();
            $table->integer('kicking_fieldGoalAttempts20_29')->nullable();
            $table->integer('kicking_fieldGoalAttempts30_39')->nullable();
            $table->integer('kicking_fieldGoalAttempts40_49')->nullable();
            $table->integer('kicking_fieldGoalAttempts50_59')->nullable();
            $table->integer('kicking_fieldGoalAttempts60_99')->nullable();
            $table->integer('kicking_fieldGoalAttempts50')->nullable();
            $table->integer('kicking_fieldGoalAttemptYards')->nullable();
            $table->decimal('kicking_fieldGoalPct', 5, 2)->nullable();
            $table->integer('kicking_fieldGoalsBlocked')->nullable();
            $table->decimal('kicking_fieldGoalsBlockedPct', 5, 2)->nullable();
            $table->integer('kicking_fieldGoalsMade')->nullable();
            $table->integer('kicking_fieldGoalsMade1_19')->nullable();
            $table->integer('kicking_fieldGoalsMade20_29')->nullable();
            $table->integer('kicking_fieldGoalsMade30_39')->nullable();
            $table->integer('kicking_fieldGoalsMade40_49')->nullable();
            $table->integer('kicking_fieldGoalsMade50_59')->nullable();
            $table->integer('kicking_fieldGoalsMade60_99')->nullable();
            $table->integer('kicking_fieldGoalsMade50')->nullable();
            $table->integer('kicking_fieldGoalsMadeYards')->nullable();
            $table->integer('kicking_fieldGoalsMissedYards')->nullable();
            $table->integer('kicking_kickoffOB')->nullable();
            $table->integer('kicking_kickoffReturns')->nullable();
            $table->integer('kicking_kickoffReturnTouchdowns')->nullable();
            $table->integer('kicking_kickoffReturnYards')->nullable();
            $table->integer('kicking_kickoffs')->nullable();
            $table->integer('kicking_kickoffYards')->nullable();
            $table->integer('kicking_longFieldGoalAttempt')->nullable();
            $table->integer('kicking_longFieldGoalMade')->nullable();
            $table->integer('kicking_longKickoff')->nullable();
            $table->integer('kicking_teamGamesPlayed')->nullable();
            $table->integer('kicking_totalKickingPoints')->nullable();
            $table->decimal('kicking_touchbackPct', 5, 2)->nullable();
            $table->integer('kicking_touchbacks')->nullable();

            // Returning stats
            $table->integer('returning_defFumbleReturns')->nullable();
            $table->integer('returning_defFumbleReturnYards')->nullable();
            $table->integer('returning_fumbleRecoveries')->nullable();
            $table->integer('returning_fumbleRecoveryYards')->nullable();
            $table->integer('returning_kickReturnFairCatches')->nullable();
            $table->decimal('returning_kickReturnFairCatchPct', 5, 2)->nullable();
            $table->integer('returning_kickReturnFumbles')->nullable();
            $table->integer('returning_kickReturnFumblesLost')->nullable();
            $table->integer('returning_kickReturns')->nullable();
            $table->integer('returning_kickReturnTouchdowns')->nullable();
            $table->integer('returning_kickReturnYards')->nullable();
            $table->integer('returning_longKickReturn')->nullable();
            $table->integer('returning_longPuntReturn')->nullable();
            $table->integer('returning_miscFumbleReturns')->nullable();
            $table->integer('returning_miscFumbleReturnYards')->nullable();
            $table->integer('returning_oppFumbleRecoveries')->nullable();
            $table->integer('returning_oppFumbleRecoveryYards')->nullable();
            $table->integer('returning_oppSpecialTeamFumbleReturns')->nullable();
            $table->integer('returning_oppSpecialTeamFumbleReturnYards')->nullable();
            $table->integer('returning_puntReturnFairCatches')->nullable();
            $table->decimal('returning_puntReturnFairCatchPct', 5, 2)->nullable();
            $table->integer('returning_puntReturnFumbles')->nullable();
            $table->integer('returning_puntReturnFumblesLost')->nullable();
            $table->integer('returning_puntReturns')->nullable();
            $table->integer('returning_puntReturnsStartedInsideThe10')->nullable();
            $table->integer('returning_puntReturnsStartedInsideThe20')->nullable();
            $table->integer('returning_puntReturnTouchdowns')->nullable();
            $table->integer('returning_puntReturnYards')->nullable();
            $table->integer('returning_specialTeamFumbleReturns')->nullable();
            $table->integer('returning_specialTeamFumbleReturnYards')->nullable();
            $table->integer('returning_teamGamesPlayed')->nullable();
            $table->decimal('returning_yardsPerKickReturn', 5, 2)->nullable();
            $table->decimal('returning_yardsPerPuntReturn', 5, 2)->nullable();
            $table->decimal('returning_yardsPerReturn', 5, 2)->nullable();

            // Punting stats
            $table->decimal('punting_avgPuntReturnYards', 5, 2)->nullable();
            $table->integer('punting_fairCatches')->nullable();
            $table->decimal('punting_grossAvgPuntYards', 5, 2)->nullable();
            $table->integer('punting_longPunt')->nullable();
            $table->decimal('punting_netAvgPuntYards', 5, 2)->nullable();
            $table->integer('punting_puntReturns')->nullable();
            $table->integer('punting_puntReturnYards')->nullable();
            $table->integer('punting_punts')->nullable();
            $table->integer('punting_puntsBlocked')->nullable();
            $table->decimal('punting_puntsBlockedPct', 5, 2)->nullable();
            $table->integer('punting_puntsInside10')->nullable();
            $table->decimal('punting_puntsInside10Pct', 5, 2)->nullable();
            $table->integer('punting_puntsInside20')->nullable();
            $table->decimal('punting_puntsInside20Pct', 5, 2)->nullable();
            $table->integer('punting_puntsOver50')->nullable();
            $table->integer('punting_puntYards')->nullable();
            $table->integer('punting_teamGamesPlayed')->nullable();
            $table->decimal('punting_touchbackPct', 5, 2)->nullable();
            $table->integer('punting_touchbacks')->nullable();

            // Scoring stats
            $table->integer('scoring_defensivePoints')->nullable();
            $table->integer('scoring_fieldGoals')->nullable();
            $table->integer('scoring_kickExtraPoints')->nullable();
            $table->integer('scoring_miscPoints')->nullable();
            $table->integer('scoring_passingTouchdowns')->nullable();
            $table->integer('scoring_receivingTouchdowns')->nullable();
            $table->integer('scoring_returnTouchdowns')->nullable();
            $table->integer('scoring_rushingTouchdowns')->nullable();
            $table->integer('scoring_totalPoints')->nullable();
            $table->decimal('scoring_totalPointsPerGame', 5, 2)->nullable();
            $table->integer('scoring_totalTouchdowns')->nullable();
            $table->integer('scoring_totalTwoPointConvs')->nullable();
            $table->integer('scoring_passingTouchdownsOf0to9Yds')->nullable();
            $table->integer('scoring_passingTouchdownsOf10to19Yds')->nullable();
            $table->integer('scoring_passingTouchdownsOf20to29Yds')->nullable();
            $table->integer('scoring_passingTouchdownsOf30to39Yds')->nullable();
            $table->integer('scoring_passingTouchdownsOf40to49Yds')->nullable();
            $table->integer('scoring_passingTouchdownsOf50PlusYds')->nullable();
            $table->integer('scoring_receivingTouchdownsOf0to9Yds')->nullable();
            $table->integer('scoring_receivingTouchdownsOf10to19Yds')->nullable();
            $table->integer('scoring_receivingTouchdownsOf20to29Yds')->nullable();
            $table->integer('scoring_receivingTouchdownsOf30to39Yds')->nullable();
            $table->integer('scoring_receivingTouchdownsOf40to49Yds')->nullable();
            $table->integer('scoring_receivingTouchdownsOf50PlusYds')->nullable();
            $table->integer('scoring_rushingTouchdownsOf0to9Yds')->nullable();
            $table->integer('scoring_rushingTouchdownsOf10to19Yds')->nullable();
            $table->integer('scoring_rushingTouchdownsOf20to29Yds')->nullable();
            $table->integer('scoring_rushingTouchdownsOf30to39Yds')->nullable();
            $table->integer('scoring_rushingTouchdownsOf40to49Yds')->nullable();
            $table->integer('scoring_rushingTouchdownsOf50PlusYds')->nullable();
            $table->integer('scoring_onePtSafetiesMade')->nullable();

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('athlete_id')->references('id')->on('nfl_espn_athletes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('espn_athlete_splits');
    }
}
