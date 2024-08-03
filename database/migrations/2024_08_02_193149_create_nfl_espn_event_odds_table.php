<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnEventOddsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_event_odds', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->unsignedBigInteger('competition_id');
            $table->string('provider_name');
            $table->string('provider_id');
            $table->string('details')->nullable();
            $table->float('over_under')->nullable();
            $table->float('spread')->nullable();
            $table->integer('over_odds')->nullable();
            $table->integer('under_odds')->nullable();
            $table->json('away_team_odds')->nullable();
            $table->json('home_team_odds')->nullable();
            $table->json('links')->nullable();
            $table->json('open_odds')->nullable();
            $table->json('current_odds')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('event_id')->on('nfl_espn_events')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_event_odds');
    }
}
