<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEspnTeamIdUidAndIsActiveToNflTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfl_teams', function (Blueprint $table) {
            $table->bigInteger('espn_team_id')->unsigned()->nullable()->after('id');
            $table->string('uid')->nullable()->after('espn_team_id');
            $table->boolean('is_active')->default(true)->after('uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfl_teams', function (Blueprint $table) {
            $table->dropColumn('espn_team_id');
            $table->dropColumn('uid');
            $table->dropColumn('is_active');
        });
    }
}
