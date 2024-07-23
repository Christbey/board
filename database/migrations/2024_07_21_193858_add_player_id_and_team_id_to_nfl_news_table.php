<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlayerIdAndTeamIdToNflNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfl_news', function (Blueprint $table) {
            $table->unsignedBigInteger('player_id')->nullable()->after('title');
            $table->unsignedBigInteger('team_id')->nullable()->after('player_id');

            // If player_id and team_id reference other tables, add foreign keys
            $table->foreign('player_id')->references('id')->on('nfl_players')->onDelete('set null');
            $table->foreign('team_id')->references('id')->on('nfl_teams')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfl_news', function (Blueprint $table) {
            $table->dropColumn('player_id');
            $table->dropColumn('team_id');

            // Drop foreign keys if they were added
            // $table->dropForeign(['player_id']);
            // $table->dropForeign(['team_id']);
        });
    }
}
