<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEspnNflDepthChartTable extends Migration
{
    public function up()
    {
        Schema::create('espn_nfl_depth_chart', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('athlete_id');
            $table->string('position');
            $table->integer('depth');
            $table->timestamps();

            // Add foreign keys if applicable
            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams');
            $table->foreign('athlete_id')->references('athlete_id')->on('nfl_espn_athletes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('espn_nfl_depth_chart');
    }
}
