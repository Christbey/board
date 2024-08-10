<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegeFootballTalentsTable extends Migration
{
    public function up()
    {
        Schema::create('college_football_talents', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->unsignedBigInteger('team_id'); // Foreign key to college_football_teams
            $table->string('school');
            $table->decimal('talent', 8, 2);
            $table->timestamps();

            // Set the foreign key constraint
            $table->foreign('team_id')->references('id')->on('college_football_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('college_football_talents', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['team_id']);
        });

        Schema::dropIfExists('college_football_talents');
    }
}
