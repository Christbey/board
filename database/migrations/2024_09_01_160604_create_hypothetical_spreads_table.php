<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('college_hypothetical_spreads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->float('spread');
            $table->boolean('correct')->default(false);
            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('college_football_games')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hypothetical_spread');
    }
};
