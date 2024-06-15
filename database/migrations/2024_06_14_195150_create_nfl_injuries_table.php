<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflInjuriesTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_injuries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->foreign('player_id')->references('player_id')->on('nfl_players')->onDelete('cascade');
            $table->unsignedBigInteger('team_id');
            $table->foreign('team_id')->references('id')->on('nfl_teams')->onDelete('cascade');
            $table->string('injury_type');
            $table->date('injury_date')->nullable();
            $table->string('designation')->nullable(); // Example: "Out", "Questionable", etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_injuries');
    }
}
