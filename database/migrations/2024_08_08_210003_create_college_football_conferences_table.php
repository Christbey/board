<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballConferencesTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_conferences', function (Blueprint $table) {
            $table->id();
            $table->string('abbreviation')->nullable(); // Allow abbreviation to be nullable
            $table->string('name');
            $table->string('short_name')->nullable(); // If short_name might also be null
            $table->string('classification')->nullable(); // If classification might also be null
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_conferences');
    }
}
