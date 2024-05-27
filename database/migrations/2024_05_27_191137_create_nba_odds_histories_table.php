<?php

// database/migrations/xxxx_xx_xx_create_nba_odds_histories_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNbaOddsHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('nba_odds_histories', function (Blueprint $table) {
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
            $table->timestamps();

            $table->foreign('odds_id')->references('id')->on('nba_odds')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nba_odds_histories');
    }
}
