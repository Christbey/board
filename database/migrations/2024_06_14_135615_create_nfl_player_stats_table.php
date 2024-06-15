<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflPlayerStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfl_player_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->foreign('player_id')->references('player_id')->on('nfl_players')->onDelete('cascade');
            $table->string('game_id');
            $table->unsignedBigInteger('team_id');
            $table->foreign('team_id')->references('id')->on('nfl_teams')->onDelete('cascade');
            $table->string('team_abv');
            $table->string('player_name');
            $table->integer('rush_yards')->nullable();
            $table->integer('carries')->nullable();
            $table->integer('rush_td')->nullable();
            $table->integer('receptions')->nullable();
            $table->integer('rec_td')->nullable();
            $table->integer('targets')->nullable();
            $table->integer('rec_yards')->nullable();
            $table->integer('games_played')->nullable();
            $table->integer('total_tackles')->nullable();
            $table->integer('fumbles_lost')->nullable();
            $table->integer('def_td')->nullable();
            $table->integer('fumbles')->nullable();
            $table->integer('fumbles_recovered')->nullable();
            $table->integer('solo_tackles')->nullable();
            $table->integer('defensive_interceptions')->nullable();
            $table->integer('qb_hits')->nullable();
            $table->integer('tfl')->nullable();
            $table->integer('pass_deflections')->nullable();
            $table->integer('sacks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfl_player_stats');
    }
}

