<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflPlayByPlayTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_play_by_play', function (Blueprint $table) {
            $table->id();
            $table->string('game_id'); // Ensure this is a string type
            $table->unsignedBigInteger('player_id')->nullable();
            $table->text('play')->nullable();
            $table->string('play_period')->nullable();
            $table->string('play_clock')->nullable();
            $table->integer('kick_yards')->nullable();
            $table->integer('receptions')->nullable();
            $table->integer('targets')->nullable();
            $table->integer('rec_yds')->nullable();
            $table->integer('pass_attempts')->nullable();
            $table->integer('pass_yds')->nullable();
            $table->integer('pass_completions')->nullable();
            $table->integer('rush_yds')->nullable();
            $table->integer('carries')->nullable();
            $table->string('down_and_distance')->nullable();
            $table->timestamps();

            $table->foreign('player_id')->references('id')->on('nfl_players')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_play_by_play');
    }
}

