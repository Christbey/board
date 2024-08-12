<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballAdvSeasonStatsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_adv_season_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('season');
            $table->unsignedBigInteger('team_id'); // Foreign key for team_id
            $table->unsignedBigInteger('conference_id')->nullable(); // Foreign key for conference_id, nullable in case no conference is provided

            // Offense Fields
            $table->integer('offense_plays');
            $table->integer('offense_drives');
            $table->decimal('offense_ppa', 10, 9);
            $table->decimal('offense_total_ppa', 15, 9);
            $table->decimal('offense_success_rate', 10, 9);
            $table->decimal('offense_explosiveness', 10, 9);
            $table->decimal('offense_power_success', 10, 9);
            $table->decimal('offense_stuff_rate', 10, 9);
            $table->decimal('offense_line_yards', 10, 9);
            $table->integer('offense_line_yards_total');
            $table->decimal('offense_second_level_yards', 10, 9);
            $table->integer('offense_second_level_yards_total');
            $table->decimal('offense_open_field_yards', 10, 9);
            $table->integer('offense_open_field_yards_total');
            $table->integer('offense_total_opportunities');
            $table->decimal('offense_points_per_opportunity', 10, 9);

            // Offense Field Position
            $table->decimal('offense_field_position_average_start', 10, 2);
            $table->decimal('offense_field_position_average_predicted_points', 10, 3);

            // Offense Havoc
            $table->decimal('offense_havoc_total', 10, 9);
            $table->decimal('offense_havoc_front_seven', 10, 9);
            $table->decimal('offense_havoc_db', 10, 9);

            // Offense Standard Downs
            $table->decimal('offense_standard_downs_rate', 10, 9);
            $table->decimal('offense_standard_downs_ppa', 10, 9);
            $table->decimal('offense_standard_downs_success_rate', 10, 9);
            $table->decimal('offense_standard_downs_explosiveness', 10, 9);

            // Offense Passing Downs
            $table->decimal('offense_passing_downs_rate', 10, 9);
            $table->decimal('offense_passing_downs_ppa', 10, 9);
            $table->decimal('offense_passing_downs_success_rate', 10, 9);
            $table->decimal('offense_passing_downs_explosiveness', 10, 9);

            // Offense Rushing Plays
            $table->decimal('offense_rushing_plays_rate', 10, 9);
            $table->decimal('offense_rushing_plays_ppa', 10, 9);
            $table->decimal('offense_rushing_plays_total_ppa', 15, 9);
            $table->decimal('offense_rushing_plays_success_rate', 10, 9);
            $table->decimal('offense_rushing_plays_explosiveness', 10, 9);

            // Offense Passing Plays
            $table->decimal('offense_passing_plays_rate', 10, 9);
            $table->decimal('offense_passing_plays_ppa', 10, 9);
            $table->decimal('offense_passing_plays_total_ppa', 15, 9);
            $table->decimal('offense_passing_plays_success_rate', 10, 9);
            $table->decimal('offense_passing_plays_explosiveness', 10, 9);

            // Defense Fields
            $table->integer('defense_plays');
            $table->integer('defense_drives');
            $table->decimal('defense_ppa', 10, 9);
            $table->decimal('defense_total_ppa', 15, 9);
            $table->decimal('defense_success_rate', 10, 9);
            $table->decimal('defense_explosiveness', 10, 9);
            $table->decimal('defense_power_success', 10, 9);
            $table->decimal('defense_stuff_rate', 10, 9);
            $table->decimal('defense_line_yards', 10, 9);
            $table->integer('defense_line_yards_total');
            $table->decimal('defense_second_level_yards', 10, 9);
            $table->integer('defense_second_level_yards_total');
            $table->decimal('defense_open_field_yards', 10, 9);
            $table->integer('defense_open_field_yards_total');
            $table->integer('defense_total_opportunities');
            $table->decimal('defense_points_per_opportunity', 10, 9);

            // Defense Field Position
            $table->decimal('defense_field_position_average_start', 10, 2);
            $table->decimal('defense_field_position_average_predicted_points', 10, 3);

            // Defense Havoc
            $table->decimal('defense_havoc_total', 10, 9);
            $table->decimal('defense_havoc_front_seven', 10, 9);
            $table->decimal('defense_havoc_db', 10, 9);

            // Defense Standard Downs
            $table->decimal('defense_standard_downs_rate', 10, 9);
            $table->decimal('defense_standard_downs_ppa', 10, 9);
            $table->decimal('defense_standard_downs_success_rate', 10, 9);
            $table->decimal('defense_standard_downs_explosiveness', 10, 9);

            // Defense Passing Downs
            $table->decimal('defense_passing_downs_rate', 10, 9);
            $table->decimal('defense_passing_downs_ppa', 10, 9);
            $table->decimal('defense_passing_downs_success_rate', 10, 9);
            $table->decimal('defense_passing_downs_explosiveness', 10, 9);

            // Defense Rushing Plays
            $table->decimal('defense_rushing_plays_rate', 10, 9);
            $table->decimal('defense_rushing_plays_ppa', 10, 9);
            $table->decimal('defense_rushing_plays_total_ppa', 15, 9);
            $table->decimal('defense_rushing_plays_success_rate', 10, 9);
            $table->decimal('defense_rushing_plays_explosiveness', 10, 9);

            // Defense Passing Plays
            $table->decimal('defense_passing_plays_rate', 10, 9);
            $table->decimal('defense_passing_plays_ppa', 10, 9);
            $table->decimal('defense_passing_plays_total_ppa', 15, 9);
            $table->decimal('defense_passing_plays_success_rate', 10, 9);
            $table->decimal('defense_passing_plays_explosiveness', 10, 9);

            $table->timestamps();

            // Foreign keys
            $table->foreign('team_id')->references('id')->on('college_football_teams')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('college_football_conferences')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_adv_season_stats');
    }
}
