<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained('nfl_espn_events')->onDelete('cascade');
            $table->integer('week_id');
            $table->unsignedBigInteger('team_id');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('team_id')
                ->references('team_id') // Assuming team_id is the primary key in nfl_espn_teams
                ->on('nfl_espn_teams')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_submissions');
    }
}
