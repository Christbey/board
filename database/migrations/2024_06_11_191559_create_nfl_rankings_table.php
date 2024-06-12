<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfl_rankings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->integer('base_elo'); // Base ELO rating -> from nfelo.app
            $table->integer('season_elo'); // Season ELO rating -> from nfl_odds table
            $table->integer('predictive_elo');
            $table->integer('power_ranking');
            $table->integer('sos');
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('team_id')->references('id')->on('nfl_teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfl_rankings');
    }
}
