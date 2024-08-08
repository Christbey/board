<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballGamesTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_games', function (Blueprint $table) {
            $table->id();
            $table->integer('season');
            $table->integer('week');
            $table->string('season_type');
            $table->string('start_date');
            $table->boolean('start_time_tbd');
            $table->boolean('completed');
            $table->boolean('neutral_site');
            $table->boolean('conference_game');
            $table->integer('attendance')->nullable();
            $table->integer('venue_id')->nullable();
            $table->string('venue')->nullable();
            $table->integer('home_id');
            $table->string('home_team');
            $table->string('home_conference')->nullable();
            $table->string('home_division')->nullable();
            $table->integer('home_points')->nullable();
            $table->json('home_line_scores')->nullable();
            $table->decimal('home_post_win_prob', 5, 2)->nullable();
            $table->integer('home_pregame_elo')->nullable();
            $table->integer('home_postgame_elo')->nullable();
            $table->integer('away_id');
            $table->string('away_team');
            $table->string('away_conference')->nullable();
            $table->string('away_division')->nullable();
            $table->integer('away_points')->nullable();
            $table->json('away_line_scores')->nullable();
            $table->decimal('away_post_win_prob', 5, 2)->nullable();
            $table->integer('away_pregame_elo')->nullable();
            $table->integer('away_postgame_elo')->nullable();
            $table->decimal('excitement_index', 5, 2)->nullable();
            $table->string('highlights')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_games');
    }
}
