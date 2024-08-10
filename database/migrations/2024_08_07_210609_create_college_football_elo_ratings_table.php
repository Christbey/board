<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballEloRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_elo_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->unsignedBigInteger('team_id'); // Foreign key for team_id
            $table->unsignedBigInteger('conference_id')->nullable(); // Foreign key for conference_id, nullable in case no conference is provided
            $table->decimal('elo', 8, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('team_id')->references('id')->on('college_football_teams')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('college_football_conferences')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_elo_ratings');
    }
}
