<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlbOddsTable extends Migration
{
    public function up()
    {
        Schema::create('mlb_odds', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('sport_title');
            $table->string('sport_key');
            $table->foreignId('home_team_id')->constrained('mlb_teams');
            $table->foreignId('away_team_id')->constrained('mlb_teams');
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
            $table->timestamp('commence_time')->nullable();
            $table->string('bookmaker_key');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mlb_odds');
    }
}
