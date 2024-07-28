<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdToNflPlayersTable extends Migration
{
    public function up()
    {
        Schema::table('nfl_players', function (Blueprint $table) {
            $table->bigInteger('team_id')->unsigned()->nullable()->after('team');
            $table->foreign('team_id')->references('id')->on('nfl_teams');
        });
    }

    public function down()
    {
        Schema::table('nfl_players', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
}
