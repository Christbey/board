<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflQbrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfl_qbr', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->decimal('qbr', 5, 2)->nullable(); // QBR value with up to 2 decimal places
            $table->integer('attempts')->nullable(); // Number of pass attempts
            $table->integer('completions')->nullable(); // Number of pass completions
            $table->integer('passing_yards')->nullable(); // Total passing yards
            $table->integer('passing_touchdowns')->nullable(); // Total passing touchdowns
            $table->integer('interceptions')->nullable(); // Total interceptions
            $table->timestamps();

            // Define foreign key constraint
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
        Schema::dropIfExists('nfl_qbr');
    }
}
