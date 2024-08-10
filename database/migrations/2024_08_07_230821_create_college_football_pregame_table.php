<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballPregameTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_pregame', function (Blueprint $table) {
            $table->unsignedBigInteger('game_id')->primary(); // Set game_id as the primary key
            $table->unsignedBigInteger('home_team_id'); // Foreign key for home team
            $table->unsignedBigInteger('away_team_id'); // Foreign key for away team
            $table->decimal('spread', 10, 2)->nullable();
            $table->decimal('home_win_prob', 5, 3)->nullable();
            $table->string('season_type');
            $table->string('season');
            $table->string('week');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('home_team_id')->references('id')->on('college_football_teams')->onDelete('cascade');
            $table->foreign('away_team_id')->references('id')->on('college_football_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_pregame');
    }
}
