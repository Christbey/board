<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballConferenceRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_conference_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->unsignedBigInteger('conference_id');
            $table->foreign('conference_id')->references('id')->on('college_football_conferences')->onDelete('cascade');
            $table->decimal('rating', 8, 3);
            $table->integer('ranking');
            $table->decimal('offense_rating', 8, 3)->nullable();
            $table->decimal('defense_rating', 8, 3)->nullable();
            $table->decimal('special_teams_rating', 8, 3)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_sp_conference_ratings');
    }
}
