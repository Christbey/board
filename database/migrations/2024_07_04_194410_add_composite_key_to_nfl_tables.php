<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompositeKeyToNflTables extends Migration
{
    public function up()
    {
        Schema::table('nfl_team_schedules', function (Blueprint $table) {
            $table->string('composite_key')->index()->nullable();
        });

        Schema::table('nfl_odds', function (Blueprint $table) {
            $table->string('composite_key')->index()->nullable();
        });
    }

    public function down()
    {
        Schema::table('nfl_team_schedules', function (Blueprint $table) {
            $table->dropColumn('composite_key');
        });

        Schema::table('nfl_odds', function (Blueprint $table) {
            $table->dropColumn('composite_key');
        });
    }
}
