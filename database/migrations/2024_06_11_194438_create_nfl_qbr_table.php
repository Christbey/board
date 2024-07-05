<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflQbrTable extends Migration
{
    public function up()
    {
        Schema::create('nfl_qbr', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('team_id');
            $table->string('game_id', 255);
            $table->decimal('qbr', 5, 2)->nullable();
            $table->integer('attempts')->nullable();
            $table->integer('completions')->nullable();
            $table->integer('passing_yards')->nullable();
            $table->integer('passing_touchdowns')->nullable();
            $table->integer('interceptions')->nullable();
            $table->timestamps();

            // Set foreign key constraint for player_id
            $table->foreign('player_id')->references('id')->on('nfl_players')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfl_qbr');
    }
}
