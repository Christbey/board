<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballPpaTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_ppa', function (Blueprint $table) {
            $table->id();
            $table->integer('season');
            $table->string('team');
            $table->string('conference');
            // Offense fields
            $table->decimal('offense_overall', 25, 20);
            $table->decimal('offense_passing', 25, 20);
            $table->decimal('offense_rushing', 25, 20);
            $table->decimal('offense_first_down', 25, 20);
            $table->decimal('offense_second_down', 25, 20);
            $table->decimal('offense_third_down', 25, 20);
            $table->decimal('offense_cumulative_total', 25, 20);
            $table->decimal('offense_cumulative_passing', 25, 20);
            $table->decimal('offense_cumulative_rushing', 25, 20);
            // Defense fields
            $table->decimal('defense_overall', 25, 20);
            $table->decimal('defense_passing', 25, 20);
            $table->decimal('defense_rushing', 25, 20);
            $table->decimal('defense_first_down', 25, 20);
            $table->decimal('defense_second_down', 25, 20);
            $table->decimal('defense_third_down', 25, 20);
            $table->decimal('defense_cumulative_total', 25, 20);
            $table->decimal('defense_cumulative_passing', 25, 20);
            $table->decimal('defense_cumulative_rushing', 25, 20);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_ppa');
    }
}
