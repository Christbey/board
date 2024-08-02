<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnWeeksTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_weeks', function (Blueprint $table) {
            $table->id();
            $table->integer('season_year');
            $table->integer('season_type');
            $table->integer('week_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_weeks');
    }
}
