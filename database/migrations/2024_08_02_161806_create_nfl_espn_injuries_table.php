<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnInjuriesTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_injuries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('athlete_id');
            $table->string('injury_id')->unique();
            $table->string('type');
            $table->string('status');
            $table->date('date')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
            $table->foreign('athlete_id')->references('athlete_id')->on('nfl_espn_athletes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_injuries');
    }
}
