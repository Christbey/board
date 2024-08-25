<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCfTeamIdToNcaaTeamsTable extends Migration
{
    public function up()
    {
        Schema::table('ncaa_teams', function (Blueprint $table) {
            $table->unsignedBigInteger('cf_team_id')->nullable()->after('id');

            // Assuming `college_football_teams` has `id` as the primary key
            $table->foreign('cf_team_id')->references('id')->on('college_football_teams')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('ncaa_teams', function (Blueprint $table) {
            $table->dropForeign(['cf_team_id']);
            $table->dropColumn('cf_team_id');
        });
    }
}
