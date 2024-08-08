<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballPregameTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_pregame', function (Blueprint $table) {
            $table->id();
            $table->integer('season');
            $table->string('season_type');
            $table->integer('week');
            $table->integer('game_id');
            $table->string('home_team');
            $table->string('away_team');
            $table->decimal('spread', 10, 2);
            $table->decimal('home_win_prob', 5, 3);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_pregame');
    }
}
