<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflPredictionsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_predictions', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->unsignedBigInteger('team_id_home');
            $table->unsignedBigInteger('team_id_away');
            $table->date('game_date');
            $table->integer('home_pts_prediction');
            $table->integer('away_pts_prediction');
            $table->decimal('home_win_percentage', 5, 2);
            $table->decimal('away_win_percentage', 5, 2);
            $table->string('season_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_predictions');
    }
}
