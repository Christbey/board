<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOddsHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('odds_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odds_id')->constrained('odds')->onDelete('cascade');
            // Head-to-Head
            $table->decimal('h2h_home_price', 8, 2)->nullable();
            $table->decimal('h2h_away_price', 8, 2)->nullable();
            // Spreads
            $table->decimal('spread_home_point', 8, 2)->nullable();
            $table->decimal('spread_away_point', 8, 2)->nullable();
            $table->decimal('spread_home_price', 8, 2)->nullable();
            $table->decimal('spread_away_price', 8, 2)->nullable();
            // Totals
            $table->decimal('total_over_point', 8, 2)->nullable();
            $table->decimal('total_under_point', 8, 2)->nullable();
            $table->decimal('total_over_price', 8, 2)->nullable();
            $table->decimal('total_under_price', 8, 2)->nullable();
            $table->timestamp('commence_time')->nullable(); // Add commence_time column

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('odds_history');
    }
}
