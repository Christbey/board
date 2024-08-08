<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_teams', function (Blueprint $table) {
            $table->id();
            $table->string('school');
            $table->string('mascot')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('alt_name1')->nullable();
            $table->string('alt_name2')->nullable();
            $table->string('alt_name3')->nullable();
            $table->string('conference')->nullable();
            $table->string('classification')->nullable();
            $table->string('color')->nullable();
            $table->string('alt_color')->nullable();
            $table->json('logos')->nullable();
            $table->string('twitter')->nullable();
            // Location details
            $table->integer('venue_id')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country_code')->nullable();
            $table->string('timezone')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('elevation')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('year_constructed')->nullable();
            $table->boolean('grass')->nullable();
            $table->boolean('dome')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_teams');
    }
}
