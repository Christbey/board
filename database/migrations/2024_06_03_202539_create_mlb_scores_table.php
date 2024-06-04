<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMlbScoresTable extends Migration
{
    public function up()
    {
        Schema::create('mlb_scores', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('sport_key');
            $table->string('sport_title');
            $table->timestamp('commence_time');
            $table->boolean('completed')->default(false);
            $table->foreignId('home_team_id')->constrained('mlb_teams');
            $table->foreignId('away_team_id')->constrained('mlb_teams');
            $table->integer('home_team_score')->nullable();
            $table->integer('away_team_score')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mlb_scores');
    }
}
