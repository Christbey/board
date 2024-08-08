<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballEloRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_elo_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('team');
            $table->string('conference');
            $table->decimal('elo', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_elo_ratings');
    }
}
