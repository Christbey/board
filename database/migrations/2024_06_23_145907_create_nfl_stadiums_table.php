<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflStadiumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfl_stadiums', function (Blueprint $table) {
            $table->id();
            $table->string('stadium_name');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('roof_type');
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->decimal('stadium_azimuth_angle', 5, 1);
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->foreign('team_id')->references('id')->on('nfl_teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfl_stadiums');
    }
}
