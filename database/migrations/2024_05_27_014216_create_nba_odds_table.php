<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNbaOddsTable extends Migration
{
    public function up()
    {
        Schema::create('nba_odds', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('sport_title');
            $table->string('sport_key');
            $table->unsignedBigInteger('home_team_id');
            $table->unsignedBigInteger('away_team_id');
            $table->string('bookmaker_key');
            $table->timestamp('commence_time');
            $table->boolean('is_live')->default(false);
            $table->decimal('h2h_home_price', 8, 2)->nullable();
            $table->decimal('h2h_away_price', 8, 2)->nullable();
            $table->decimal('spread_home_point', 8, 2)->nullable();
            $table->decimal('spread_away_point', 8, 2)->nullable();
            $table->decimal('spread_home_price', 8, 2)->nullable();
            $table->decimal('spread_away_price', 8, 2)->nullable();
            $table->decimal('total_over_point', 8, 2)->nullable();
            $table->decimal('total_under_point', 8, 2)->nullable();
            $table->decimal('total_over_price', 8, 2)->nullable();
            $table->decimal('total_under_price', 8, 2)->nullable();
            $table->timestamps();


            $table->foreign('home_team_id')->references('id')->on('nba_teams');
            $table->foreign('away_team_id')->references('id')->on('nba_teams');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nba_odds');
    }
}
