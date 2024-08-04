<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnEventPredictorsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_event_predictors', function (Blueprint $table) {
            $table->id();
            $table->integer('rank');
            $table->string('total');
            $table->decimal('value', 8, 3);
            $table->string('display_value');
            $table->unsignedBigInteger('predictor_competition_id');
            $table->unsignedBigInteger('projected_winner_id');
            $table->unsignedBigInteger('cover_id');
            $table->unsignedBigInteger('projected_cover_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_event_predictors');
    }
}
