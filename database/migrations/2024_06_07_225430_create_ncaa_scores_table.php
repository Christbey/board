<?php

// database/migrations/xxxx_xx_xx_create_ncaa_scores_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNcaaScoresTable extends Migration
{
    public function up()
    {
        Schema::create('ncaa_scores', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('sport_key');
            $table->string('sport_title');
            $table->timestamp('commence_time');
            $table->boolean('completed');
            $table->foreignId('home_team_id')->constrained('ncaa_teams');
            $table->foreignId('away_team_id')->constrained('ncaa_teams');
            $table->integer('home_team_score')->nullable();
            $table->integer('away_team_score')->nullable();
            $table->timestamp('last_update');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ncaa_scores');
    }
}
