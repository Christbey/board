<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflTeamSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_team_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->string('season_type');
            $table->string('away');
            $table->unsignedBigInteger('team_id_home');
            $table->foreign('team_id_home')->references('id')->on('nfl_teams')->onDelete('cascade');
            $table->date('game_date'); // Changed to date type
            $table->string('game_status');
            $table->string('game_week');
            $table->unsignedBigInteger('team_id_away');
            $table->foreign('team_id_away')->references('id')->on('nfl_teams')->onDelete('cascade');
            $table->string('home');
            $table->string('away_result')->nullable();
            $table->integer('home_pts')->default(0);
            $table->string('game_time')->nullable(); // Changed to string type
            $table->string('home_result')->nullable();
            $table->integer('away_pts')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_team_schedules');
    }
}
