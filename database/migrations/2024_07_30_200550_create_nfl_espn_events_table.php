<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnEventsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('week_id');
            $table->string('event_id')->unique();
            $table->string('uid')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->integer('attendance')->nullable();
            $table->boolean('neutral_site')->nullable();
            $table->boolean('conference_competition')->nullable();
            $table->boolean('play_by_play_available')->nullable();
            $table->string('venue_id')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_city')->nullable();
            $table->string('venue_state')->nullable();
            $table->boolean('venue_indoor')->nullable();
            $table->unsignedBigInteger('home_team_id');
            $table->string('home_team_score')->nullable();
            $table->string('home_team_record')->nullable();
            $table->unsignedBigInteger('away_team_id');
            $table->string('away_team_score')->nullable();
            $table->string('away_team_record')->nullable();
            $table->boolean('status_type_completed')->nullable();
            $table->string('status_type_detail')->nullable();
            $table->timestamps();

            $table->foreign('week_id')->references('id')->on('nfl_espn_weeks')->onDelete('cascade');
            $table->foreign('home_team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
            $table->foreign('away_team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_events');
    }
}
