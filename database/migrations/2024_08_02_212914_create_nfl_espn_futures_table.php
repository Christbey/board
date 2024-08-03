<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnFuturesTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_futures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('future_id');
            $table->string('name');
            $table->string('display_name');
            $table->unsignedBigInteger('provider_id');
            $table->string('provider_name');
            $table->unsignedBigInteger('athlete_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('value');
            $table->timestamps();

            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams');
            $table->foreign('athlete_id')->references('athlete_id')->on('nfl_espn_athletes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_futures');
    }
}
