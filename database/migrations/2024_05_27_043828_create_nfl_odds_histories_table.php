<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflOddsHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_odds_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('odds_id');
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
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('odds_id')->references('id')->on('nfl_odds')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_odds_histories');
    }
}
