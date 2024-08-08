<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballPlayWpTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_play_wp', function (Blueprint $table) {
            $table->id();
            $table->integer('game_id');
            $table->bigInteger('play_id');
            $table->text('play_text');
            $table->integer('home_id');
            $table->string('home', 255);
            $table->integer('away_id');
            $table->string('away', 255);
            $table->decimal('spread', 10, 2);
            $table->boolean('home_ball');
            $table->integer('home_score');
            $table->integer('away_score');
            $table->integer('time_remaining')->nullable();
            $table->integer('yard_line')->nullable();
            $table->integer('down');
            $table->integer('distance');
            $table->decimal('home_win_prob', 10, 9);
            $table->integer('play_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_play_wp');
    }
}
