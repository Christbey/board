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
            $table->integer('game_id');
            $table->integer('season');
            $table->integer('week');
            $table->string('team');
            $table->string('conference');
            $table->string('opponent');
            // Offense fields
            $table->decimal('offense_overall', 20, 18);
            $table->decimal('offense_passing', 20, 18);
            $table->decimal('offense_rushing', 20, 18);
            $table->decimal('offense_first_down', 20, 18);
            $table->decimal('offense_second_down', 20, 18);
            $table->decimal('offense_third_down', 20, 18);
            // Defense fields
            $table->decimal('defense_overall', 20, 18);
            $table->decimal('defense_passing', 20, 18);
            $table->decimal('defense_rushing', 20, 18);
            $table->decimal('defense_first_down', 20, 18);
            $table->decimal('defense_second_down', 20, 18);
            $table->decimal('defense_third_down', 20, 18);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_game_ppa');
    }
}
