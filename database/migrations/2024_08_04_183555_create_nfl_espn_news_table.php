<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnNewsTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_espn_news', function (Blueprint $table) {
            $table->id();
            $table->string('headline');
            $table->text('description')->nullable();
            $table->string('url')->unique();
            $table->string('image_url')->nullable();
            $table->string('byline')->nullable();
            $table->dateTime('published')->nullable();
            $table->dateTime('last_modified')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('athlete_id')->nullable();
            $table->timestamps();

            // Set up foreign key constraints
            $table->foreign('team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('set null');
            $table->foreign('athlete_id')->references('athlete_id')->on('nfl_espn_athletes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_espn_news');
    }
}
