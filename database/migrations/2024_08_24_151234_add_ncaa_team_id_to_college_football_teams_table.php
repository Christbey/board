<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNcaaTeamIdToCollegeFootballTeamsTable extends Migration
{
    public function up()
    {
        Schema::table('college_football_teams', function (Blueprint $table) {
            $table->unsignedBigInteger('ncaa_team_id')->nullable()->after('id');
            $table->foreign('ncaa_team_id')->references('id')->on('ncaa_teams')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('college_football_teams', function (Blueprint $table) {
            $table->dropForeign(['ncaa_team_id']);
            $table->dropColumn('ncaa_team_id');
        });
    }
}
