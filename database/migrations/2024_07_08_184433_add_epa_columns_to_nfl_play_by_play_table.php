<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEpaColumnsToNflPlayByPlayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfl_play_by_play', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('game_id');
            $table->foreign('team_id')->references('id')->on('nfl_teams');
            $table->float('expected_points_before')->nullable()->after('down_and_distance');
            $table->float('expected_points_after')->nullable()->after('expected_points_before');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfl_play_by_play', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
            $table->dropColumn('expected_points_before');
            $table->dropColumn('expected_points_after');
        });
    }
}
