<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballRankingsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_rankings', function (Blueprint $table) {
            $table->id();
            $table->integer('season');
            $table->string('season_type');
            $table->integer('week');
            $table->string('poll');
            $table->integer('rank');
            $table->string('school');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('conference_id')->nullable();

            $table->foreign('team_id')->references('id')->on('college_football_teams')->onDelete('set null');
            $table->foreign('conference_id')->references('id')->on('college_football_conferences')->onDelete('set null');
            $table->integer('first_place_votes')->nullable();
            $table->integer('points')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_rankings');
    }
}
