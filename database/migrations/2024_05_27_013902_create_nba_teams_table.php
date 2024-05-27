<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNbaTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('nba_teams', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(600);
            $table->string('name');
            $table->string('abbreviation');
            $table->string('conference');
            $table->string('division');
            $table->string('team_mascot')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('location')->nullable();
            $table->string('stadium')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nba_teams');
    }
}
