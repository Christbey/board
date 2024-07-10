<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEloRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('elo_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->decimal('rating', 10, 2);
            $table->timestamps();

            $table->foreign('team_id')->references('id')->on('nfl_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('elo_ratings');
    }
}
