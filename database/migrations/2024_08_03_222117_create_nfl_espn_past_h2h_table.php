<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNflEspnPastH2hTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfl_espn_past_h2h', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id');
            $table->unsignedBigInteger('home_team_id');
            $table->unsignedBigInteger('away_team_id');
            $table->decimal('spread', 5, 2);
            $table->decimal('over_odds', 5, 2);
            $table->decimal('under_odds', 5, 2);
            $table->decimal('away_team_money_line_odds', 5, 2);
            $table->decimal('away_team_spread_odds', 5, 2);
            $table->boolean('away_team_spread_winner');
            $table->boolean('away_team_money_line_winner');
            $table->decimal('home_team_money_line_odds', 5, 2);
            $table->decimal('home_team_spread_odds', 5, 2);
            $table->boolean('home_team_spread_winner');
            $table->boolean('home_team_money_line_winner');
            $table->timestamp('line_date');
            $table->decimal('total_line', 5, 2);
            $table->string('total_result');
            $table->boolean('moneyline_winner');
            $table->boolean('spread_winner');
            $table->timestamps();

            // Adding foreign key constraints
            $table->foreign('home_team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
            $table->foreign('away_team_id')->references('team_id')->on('nfl_espn_teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfl_espn_past_h2h');
    }
}
