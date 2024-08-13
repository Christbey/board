<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballGamePpaTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_game_ppa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id'); // Foreign key for game_id
            $table->integer('season');
            $table->integer('week');
            $table->unsignedBigInteger('team_id'); // Foreign key for team_id
            $table->unsignedBigInteger('conference_id'); // Foreign key for conference_id
            $table->unsignedBigInteger('opponent_id'); // Foreign key for opponent_id
            // Offense fields
            $table->decimal('offense_overall', 20, 18)->nullable();
            $table->decimal('offense_passing', 20, 18)->nullable();
            $table->decimal('offense_rushing', 20, 18)->nullable();
            $table->decimal('offense_first_down', 20, 18)->nullable();
            $table->decimal('offense_second_down', 20, 18)->nullable();
            $table->decimal('offense_third_down', 20, 18)->nullable();
            // Defense fields
            $table->decimal('defense_overall', 20, 18)->nullable();
            $table->decimal('defense_passing', 20, 18)->nullable();
            $table->decimal('defense_rushing', 20, 18)->nullable();
            $table->decimal('defense_first_down', 20, 18)->nullable();
            $table->decimal('defense_second_down', 20, 18)->nullable();
            $table->decimal('defense_third_down', 20, 18)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('game_id')->references('id')->on('college_football_games')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('college_football_teams')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('college_football_conferences')->onDelete('cascade');
            $table->foreign('opponent_id')->references('id')->on('college_football_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_game_ppa');
    }
}
