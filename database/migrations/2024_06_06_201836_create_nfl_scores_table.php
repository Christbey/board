<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflScoresTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nfl_scores', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('sport_key');
            $table->string('sport_title');
            $table->timestamp('commence_time');
            $table->foreignId('home_team_id')->constrained('nfl_teams');
            $table->foreignId('away_team_id')->constrained('nfl_teams');
            $table->integer('home_team_score')->nullable();
            $table->integer('away_team_score')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_scores');
    }
}
