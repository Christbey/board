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
            $table->integer('year');
            $table->string('team');
            $table->string('conference')->nullable();
            $table->decimal('rating', 8, 2)->nullable();
            $table->integer('ranking')->nullable();
            $table->decimal('second_order_wins', 8, 2)->nullable();
            $table->decimal('sos', 8, 2)->nullable();
            // Offense fields
            $table->integer('offense_ranking')->nullable();
            $table->decimal('offense_rating', 8, 2)->nullable();
            $table->decimal('offense_success', 8, 2)->nullable();
            $table->decimal('offense_explosiveness', 8, 2)->nullable();
            $table->decimal('offense_rushing', 8, 2)->nullable();
            $table->decimal('offense_passing', 8, 2)->nullable();
            $table->decimal('offense_standard_downs', 8, 2)->nullable();
            $table->decimal('offense_passing_downs', 8, 2)->nullable();
            $table->decimal('offense_run_rate', 8, 2)->nullable();
            $table->decimal('offense_pace', 8, 2)->nullable();
            // Defense fields
            $table->integer('defense_ranking')->nullable();
            $table->decimal('defense_rating', 8, 2)->nullable();
            $table->decimal('defense_success', 8, 2)->nullable();
            $table->decimal('defense_explosiveness', 8, 2)->nullable();
            $table->decimal('defense_rushing', 8, 2)->nullable();
            $table->decimal('defense_passing', 8, 2)->nullable();
            $table->decimal('defense_standard_downs', 8, 2)->nullable();
            $table->decimal('defense_passing_downs', 8, 2)->nullable();
            $table->decimal('defense_havoc_total', 8, 2)->nullable();
            $table->decimal('defense_havoc_front_seven', 8, 2)->nullable();
            $table->decimal('defense_havoc_db', 8, 2)->nullable();
            // Special Teams fields
            $table->decimal('special_teams_rating', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_sp_ratings');
    }
}
