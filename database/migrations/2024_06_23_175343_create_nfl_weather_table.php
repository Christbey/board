<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflWeatherTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_weather', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stadium_id');
            $table->unsignedBigInteger('game_id');
            $table->date('date');
            $table->string('game_time');
            $table->json('temp');
            $table->decimal('wind', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('stadium_id')->references('id')->on('nfl_stadiums')->onDelete('cascade');
            $table->foreign('game_id')->references('id')->on('nfl_team_schedules')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_weather');
    }
}
