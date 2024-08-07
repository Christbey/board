<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnTeamStatsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_team_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('season');
            $table->unsignedBigInteger('team_id');
            $table->string('category');
            $table->string('stat_name');
            $table->float('stat_value');
            $table->string('stat_display_value');
            $table->integer('stat_rank')->nullable();
            $table->string('stat_rank_display_value')->nullable();
            $table->timestamps();

            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_team_stats');
    }
}
