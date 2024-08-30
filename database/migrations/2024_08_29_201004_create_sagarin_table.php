<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSagarinTable extends Migration
{
    public function up()
    {
        Schema::create('sagarins', function (Blueprint $table) {
            // The ID of the sagarin table will be the same as the ID of the college_football_teams table
            $table->unsignedBigInteger('id')->primary();
            $table->string('team_name');
            $table->decimal('rating', 8, 2);
            $table->timestamps();

            // Set up foreign key constraint
            $table->foreign('id')->references('id')->on('college_football_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sagarin');
    }
}