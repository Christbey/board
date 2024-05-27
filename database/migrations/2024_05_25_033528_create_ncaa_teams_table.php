<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNcaaTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ncaa_teams', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(100);
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->string('conference')->nullable();
            $table->string('division')->nullable();
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ncaa_teams');
    }
}