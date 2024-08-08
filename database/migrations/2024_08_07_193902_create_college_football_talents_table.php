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
            $table->string('school');
            $table->decimal('talent', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('college_football_talents');
    }
}
