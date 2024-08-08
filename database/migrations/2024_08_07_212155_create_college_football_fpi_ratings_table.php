<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballFpiRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_fpi_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('team');
            $table->string('conference');
            $table->decimal('fpi', 8, 3);
            // Resume Ranks fields
            $table->integer('strength_of_record')->nullable();
            $table->integer('resume_fpi')->nullable();
            $table->integer('average_win_probability')->nullable();
            $table->integer('strength_of_schedule')->nullable();
            $table->integer('remaining_strength_of_schedule')->nullable();
            $table->integer('game_control')->nullable();
            // Efficiencies fields
            $table->decimal('efficiency_overall', 8, 3)->nullable();
            $table->decimal('efficiency_offense', 8, 3)->nullable();
            $table->decimal('efficiency_defense', 8, 3)->nullable();
            $table->decimal('efficiency_special_teams', 8, 3)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_fpi_ratings');
    }
}
