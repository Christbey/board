<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballAdvGameStatsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_adv_game_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('game_id')->unique();
            $table->integer('season');
            $table->integer('week');
            $table->string('team');
            $table->string('opponent');
            $table->integer('offense_plays')->nullable();
            $table->integer('offense_drives')->nullable();
            $table->float('offense_ppa')->nullable();
            $table->float('offense_total_ppa')->nullable();
            $table->float('offense_success_rate')->nullable();
            $table->float('offense_explosiveness')->nullable();
            $table->float('offense_power_success')->nullable();
            $table->float('offense_stuff_rate')->nullable();
            $table->float('offense_line_yards')->nullable();
            $table->integer('offense_line_yards_total')->nullable();
            $table->float('offense_second_level_yards')->nullable();
            $table->integer('offense_second_level_yards_total')->nullable();
            $table->float('offense_open_field_yards')->nullable();
            $table->integer('offense_open_field_yards_total')->nullable();
            $table->float('offense_standard_downs_ppa')->nullable();
            $table->float('offense_standard_downs_success_rate')->nullable();
            $table->float('offense_standard_downs_explosiveness')->nullable();
            $table->float('offense_passing_downs_ppa')->nullable();
            $table->float('offense_passing_downs_success_rate')->nullable();
            $table->float('offense_passing_downs_explosiveness')->nullable();
            $table->float('offense_rushing_plays_ppa')->nullable();
            $table->float('offense_rushing_plays_total_ppa')->nullable();
            $table->float('offense_rushing_plays_success_rate')->nullable();
            $table->float('offense_rushing_plays_explosiveness')->nullable();
            $table->float('offense_passing_plays_ppa')->nullable();
            $table->float('offense_passing_plays_total_ppa')->nullable();
            $table->float('offense_passing_plays_success_rate')->nullable();
            $table->float('offense_passing_plays_explosiveness')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_adv_game_stats');
    }
}
