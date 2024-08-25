<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNcaaOddsIdToCollegeFootballGamesTable extends Migration
{
    public function up()
    {
        Schema::table('college_football_games', function (Blueprint $table) {
            $table->unsignedBigInteger('ncaa_odds_id')->nullable()->after('id'); // Adjust 'some_column' to place this column where needed
            $table->foreign('ncaa_odds_id')->references('id')->on('ncaa_odds')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('college_football_games', function (Blueprint $table) {
            $table->dropForeign(['ncaa_odds_id']);
            $table->dropColumn('ncaa_odds_id');
        });
    }
}
