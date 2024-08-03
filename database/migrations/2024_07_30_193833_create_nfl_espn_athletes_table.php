<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnAthletesTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_athletes', function (Blueprint $table) {
            // Remove the id() method and define athlete_id as the primary key
            $table->unsignedBigInteger('athlete_id')->primary();
            $table->unsignedBigInteger('team_id');
            $table->string('jersey')->nullable();
            $table->integer('season_year');
            $table->string('uid')->nullable();
            $table->string('guid')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('display_name')->nullable();
            $table->string('short_name')->nullable();
            $table->integer('weight')->nullable();
            $table->string('display_weight')->nullable();
            $table->integer('height')->nullable();
            $table->string('display_height')->nullable();
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('debut_year')->nullable();
            $table->string('position')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_athletes');
    }
}
