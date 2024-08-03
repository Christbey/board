<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnTeamProjectionsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_team_projections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->float('chance_to_win_division');
            $table->float('projected_wins');
            $table->float('projected_losses');
            $table->timestamps();

            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_team_projections');
    }
}
