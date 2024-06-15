<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfl_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id')->unique();
            $table->string('longName')->nullable();
            $table->string('team')->nullable();
            $table->string('jerseyNum')->nullable();
            $table->string('pos')->nullable();
            $table->string('exp')->nullable();
            $table->string('school')->nullable();
            $table->integer('age')->nullable();
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
        Schema::dropIfExists('players');
    }
}
