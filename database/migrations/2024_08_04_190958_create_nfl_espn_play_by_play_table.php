<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnPlayByPlayTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_play_by_play', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->string('sequenceNumber');
            $table->string('type_id');
            $table->string('type_text');
            $table->string('type_abbreviation')->nullable();
            $table->text('text');
            $table->string('shortText')->nullable();
            $table->string('alternativeText')->nullable();
            $table->string('shortAlternativeText')->nullable();
            $table->integer('awayScore')->nullable();
            $table->integer('homeScore')->nullable();
            $table->integer('period_number')->nullable();
            $table->float('clock_value')->nullable();
            $table->string('clock_displayValue')->nullable();
            $table->boolean('scoringPlay');
            $table->integer('scoreValue')->nullable();
            $table->timestamp('modified')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('athlete_id')->nullable();
            $table->string('position')->nullable();
            $table->string('participant_type')->nullable();
            $table->integer('participant_order')->nullable();
            $table->timestamp('wallclock')->nullable();
            $table->unsignedBigInteger('drive_id')->nullable();
            $table->integer('start_down')->nullable();
            $table->integer('start_distance')->nullable();
            $table->integer('start_yardLine')->nullable();
            $table->integer('start_yardsToEndzone')->nullable();
            $table->string('start_downDistanceText')->nullable();
            $table->string('start_shortDownDistanceText')->nullable();
            $table->string('start_possessionText')->nullable();
            $table->unsignedBigInteger('start_team_id')->nullable();
            $table->integer('end_down')->nullable();
            $table->integer('end_distance')->nullable();
            $table->integer('end_yardLine')->nullable();
            $table->integer('end_yardsToEndzone')->nullable();
            $table->string('end_downDistanceText')->nullable();
            $table->string('end_shortDownDistanceText')->nullable();
            $table->string('end_possessionText')->nullable();
            $table->unsignedBigInteger('end_team_id')->nullable();
            $table->integer('statYardage')->nullable();
            $table->timestamps();

            // Adding foreign keys
            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('set null');
            $table->foreign('athlete_id')->references('athlete_id')->on('nfl_espn_athletes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_play_by_play');
    }
}
