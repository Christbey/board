<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballRostersTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_rosters', function (Blueprint $table) {
            $table->string('player_id')->primary(); // Set player_id as the primary key
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedBigInteger('team_id'); // Replace 'team' with 'team_id'
            $table->integer('weight')->nullable();
            $table->integer('height')->nullable();
            $table->integer('jersey')->nullable();
            $table->integer('year')->nullable();
            $table->string('position')->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_state')->nullable();
            $table->string('home_country')->nullable();
            $table->decimal('home_latitude', 10, 7)->nullable();
            $table->decimal('home_longitude', 10, 7)->nullable();
            $table->string('home_county_fips')->nullable();
            $table->json('recruit_ids')->nullable();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('team_id')->references('id')->on('college_football_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_rosters');
    }
}
