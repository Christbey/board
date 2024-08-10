<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballSpRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_sp_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id'); // Foreign key for the team
            $table->unsignedBigInteger('conference_id'); // Foreign key for the conference
            $table->integer('year');
            $table->decimal('rating', 8, 4)->nullable();
            $table->integer('ranking')->nullable();
            $table->decimal('second_order_wins', 8, 4)->nullable();
            $table->decimal('sos', 8, 4)->nullable();
            $table->integer('offense_ranking')->nullable();
            $table->decimal('offense_rating', 8, 4)->nullable();
            $table->decimal('offense_success', 8, 4)->nullable();
            $table->decimal('offense_explosiveness', 8, 4)->nullable();
            $table->decimal('offense_rushing', 8, 4)->nullable();
            $table->decimal('offense_passing', 8, 4)->nullable();
            $table->decimal('offense_standard_downs', 8, 4)->nullable();
            $table->decimal('offense_passing_downs', 8, 4)->nullable();
            $table->decimal('offense_run_rate', 8, 4)->nullable();
            $table->decimal('offense_pace', 8, 4)->nullable();
            $table->integer('defense_ranking')->nullable();
            $table->decimal('defense_rating', 8, 4)->nullable();
            $table->decimal('defense_success', 8, 4)->nullable();
            $table->decimal('defense_explosiveness', 8, 4)->nullable();
            $table->decimal('defense_rushing', 8, 4)->nullable();
            $table->decimal('defense_passing', 8, 4)->nullable();
            $table->decimal('defense_standard_downs', 8, 4)->nullable();
            $table->decimal('defense_passing_downs', 8, 4)->nullable();
            $table->decimal('defense_havoc_total', 8, 4)->nullable();
            $table->decimal('defense_havoc_front_seven', 8, 4)->nullable();
            $table->decimal('defense_havoc_db', 8, 4)->nullable();
            $table->decimal('special_teams_rating', 8, 4)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('team_id')->references('id')->on('college_football_teams')->onDelete('cascade');
            $table->foreign('conference_id')->references('id')->on('college_football_conferences')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_sp_ratings');
    }
}
